<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paymentsub;
use App\Models\Pengajuan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentSubController extends Controller
{
    // List all payment subs
    public function index()
    {
        $paymentsubs = Paymentsub::with('pengajuan')->get();
        return response()->json($paymentsubs, 200);
    }

    // Store a new payment sub
    public function store(Request $request)
    {
        $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan,id',
            'amount_paid' => 'required|numeric|min:0',
            'method' => 'required|in:transfer_bank,cash,credit_card,debit_card,e_wallet',
            'path_file' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        $paymentsub = Paymentsub::create([
            'pengajuan_id' => $request->pengajuan_id,
            'amount_paid' => $request->amount_paid,
            'method' => $request->method,
            'path_file' => $request->path_file,
            'paid_at' => $request->paid_at,
        ]);

        return response()->json([
            'message' => 'Pembayaran sub berhasil dibuat',
            'paymentsub' => $paymentsub
        ], 201);
    }

    // Show detail payment sub
    public function show($id)
    {
        $paymentsub = Paymentsub::with('pengajuan')->find($id);
        if (!$paymentsub) {
            return response()->json(['message' => 'PaymentSub not found'], 404);
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
            'value' => 'required|string', // nilai yang ingin di-encode
        ]);

        $barcodeValue = $request->value;

        // Generate QR code sebagai base64 PNG
        $qrImage = QrCode::format('png')->size(300)->generate($barcodeValue);
        $base64 = base64_encode($qrImage);

        // Simpan base64 ke database, atau simpan file ke storage dan simpan path-nya
        // Contoh: simpan base64 ke kolom barcode di Paymentsub
        $paymentsub = Paymentsub::firstOrCreate(
            ['pengajuan_id' => $request->pengajuan_id],
            ['barcode' => $base64]
        );
        if (!$paymentsub->wasRecentlyCreated) {
            $paymentsub->barcode = $base64;
            $paymentsub->save();
        }

        return response()->json([
            'message' => 'Barcode generated',
            'barcode_base64' => $base64,
            'paymentsub' => $paymentsub
        ], 200);
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
}
