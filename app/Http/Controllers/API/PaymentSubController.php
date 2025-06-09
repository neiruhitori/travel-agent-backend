<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paymentsub;
use App\Models\Pengajuan;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentSubController extends Controller
{
    // List all payment subs
    public function index()
    {
        $paymentsubs = Paymentsub::with('pengajuan')->get();
        return response()->json($paymentsubs, 200);
    }

    // Store a new payment sub
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'pengajuan_id' => 'required|exists:pengajuan,id',
    //         'amount_paid' => 'required|numeric|min:0',
    //         'method' => 'required|in:transfer_bank,cash,credit_card,debit_card,e_wallet',
    //         'path_file' => 'nullable|string',
    //         'paid_at' => 'nullable|date',
    //     ]);

    //     $paymentsub = Paymentsub::create([
    //         'pengajuan_id' => $request->pengajuan_id,
    //         'amount_paid' => $request->amount_paid,
    //         'method' => $request->method,
    //         'path_file' => $request->path_file,
    //         'paid_at' => $request->paid_at,
    //     ]);

    //     return response()->json([
    //         'message' => 'Pembayaran sub berhasil dibuat',
    //         'paymentsub' => $paymentsub
    //     ], 201);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan,id',
            'amount_paid' => 'required|numeric|min:0',
            'method' => 'required|in:transfer_bank,cash,credit_card,debit_card,e_wallet',
            'path_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        try {
            // Cari paymentsub berdasarkan pengajuan_id
            $paymentsub = Paymentsub::where('pengajuan_id', $request->pengajuan_id)->first();

            if (!$paymentsub) {
                return response()->json([
                    'message' => 'Data pembayaran belum tersedia untuk pengajuan ini.'
                ], 404);
            }

            if ($request->hasFile('path_file')) {
                $storage = Storage::disk('public');
                $imageName = 'paymentsub/' . Str::random(32) . "." . $request->path_file->getClientOriginalExtension();

                // Hapus file lama jika ada
                if ($paymentsub->path_file && $storage->exists($paymentsub->path_file)) {
                    $storage->delete($paymentsub->path_file);
                }

                // Simpan file baru
                $storage->put($imageName, file_get_contents($request->path_file));

                // Update data paymentsub
                $paymentsub->update([
                    'amount_paid' => $request->amount_paid,
                    'method' => $request->method,
                    'path_file' => $imageName,
                    'paid_at' => now(),
                ]);
            }

            return response()->json([
                'message' => 'Bukti pembayaran berhasil diupdate',
                'paymentsub' => [
                    ...$paymentsub->toArray(),
                    'path_file' => url('storage/' . $paymentsub->path_file)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengupdate pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show detail payment sub
    public function show($id)
    {
        $paymentsub = Paymentsub::with('pengajuan')->find($id);
        if (!$paymentsub) {
            return response()->json(['message' => 'PaymentSub not found'], 404);
        }
        // Tambahkan URL lengkap untuk path_file
        if ($paymentsub->path_file) {
            $paymentsub->path_file = url('storage/' . $paymentsub->path_file);
        }

        return response()->json($paymentsub, 200);
    }

    // Update payment sub
    public function update(Request $request, $id)
    {
        $paymentsub = Paymentsub::find($id);
        if (!$paymentsub) {
            return response()->json(['message' => 'PaymentSub not found'], 404);
        }
        $request->validate([
            'amount_paid' => 'sometimes|numeric|min:0',
            'method' => 'sometimes|in:transfer_bank,cash,credit_card,debit_card,e_wallet',
            'path_file' => 'nullable|string',
            'paid_at' => 'nullable|date',
            'verified_by' => 'nullable|string',
            'verified_at' => 'nullable|date',
        ]);
        $paymentsub->update($request->all());
        return response()->json($paymentsub, 200);
    }

    // Delete payment sub
    public function destroy($id)
    {
        $paymentsub = Paymentsub::find($id);
        if (!$paymentsub) {
            return response()->json(['message' => 'PaymentSub not found'], 404);
        }
        $paymentsub->delete();
        return response()->json(['message' => 'PaymentSub berhasil dihapus'], 200);
    }

    // Generate barcode untuk pengajuan tertentu
    public function generateBarcode(Request $request)
    {
        $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan,id',
            'value' => 'required|string',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        try {
            // Create QR code
            $qr = QrCode::create($request->value)
                ->setSize(300)
                ->setMargin(10)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));

            // Create writer and generate QR code
            $writer = new PngWriter();
            $result = $writer->write($qr);

            // Get as base64
            $base64 = base64_encode($result->getString());

            // Save to database with amount_paid
            $paymentsub = Paymentsub::firstOrCreate(
                ['pengajuan_id' => $request->pengajuan_id],
                [
                    'barcode' => $base64,
                    'amount_paid' => $request->amount_paid, // Add amount_paid here
                    'method' => 'transfer_bank' // Add a default method or make it required in the request
                ]
            );

            if (!$paymentsub->wasRecentlyCreated) {
                $paymentsub->barcode = $base64;
                $paymentsub->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Barcode berhasil dibuat',
                'data' => [
                    'barcode_base64' => $base64,
                    'paymentsub' => $paymentsub
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat barcode: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get barcode by pengajuan_id
    public function getBarcodeByPengajuan($pengajuan_id)
    {
        $paymentsub = Paymentsub::where('pengajuan_id', $pengajuan_id)->first();
        if (!$paymentsub || !$paymentsub->barcode) {
            return response()->json(['message' => 'Barcode not found'], 404);
        }
        return response()->json(['barcode' => $paymentsub->barcode], 200);
    }

    // INITIAL FUNCT
    public function verifyPayment(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:lunas,ditolak',
            'verified_by' => 'required|string',
        ]);

        $paymentsub = Paymentsub::find($id);
        if (!$paymentsub) {
            return response()->json(['message' => 'PaymentSub not found'], 404);
        }

        $paymentsub->update([
            'status' => $request->status,
            'verified_by' => $request->verified_by,
            'verified_at' => now(),
        ]);

        return response()->json(['message' => 'Pembayaran berhasil diverifikasi', 'paymentsub' => $paymentsub], 200);
    }

    public function notifications()
    {
        // Hanya ambil paymentsub yang sudah ada file bukti pembayaran
        $list = Paymentsub::with('pengajuan')
            ->whereNotNull('path_file')
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'pengajuan_id' => $item->pengajuan_id,
                    'institution' => $item->pengajuan->institution ?? '-',
                    'applicant' => $item->pengajuan->applicant ?? '-',
                    'uploaded_at' => $item->updated_at,
                    'bukti_url' => $item->path_file ? asset('storage/' . $item->path_file) : null,
                ];
            });
        return response()->json($list);
    }
}
