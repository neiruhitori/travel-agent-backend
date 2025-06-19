<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Models\Destination;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;

class PengajuanController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if ($user && $user->role === 'customer') {
            $pengajuan = Pengajuan::where('user_id', $user->id)->with('destination')->get();
        } else {
            $pengajuan = Pengajuan::with('destination')->get();
        }
        $pengajuan = $pengajuan->map(function ($item) {
            $itemArray = $item->toArray();
            $itemArray['destination'] = $item->destination ? $item->destination->location : null;
            return $itemArray;
        });
        return response()->json($pengajuan, 200);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'institution' => 'required|string|max:255',
            'applicant' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'participants' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:menunggu_konfirmasi,menunggu_persetujuan,disetujui,dalam_perjalanan,menunggu_pembayaran,lunas,ditolak',
        ]);

        $pengajuan = Pengajuan::create([
            'user_id' => $request->user_id,
            'institution' => $request->institution,
            'applicant' => $request->applicant,
            'email' => $request->email,
            'destination_id' => $request->destination_id,
            'vehicle_id' => $request->vehicle_id,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'participants' => $request->participants,
            'notes' => $request->notes,
            'status' => $request->status ?? 'menunggu_konfirmasi',
        ]);

        return response()->json(['message' => 'Pengajuan berhasil disimpan', 'pengajuan' => $pengajuan], 201);
    }

    // public function show($id)
    // {
    //     $user = Auth::user();
    //     $pengajuan = Pengajuan::with('destination')->find($id);
    //     if (!$pengajuan) {
    //         return response()->json(['message' => 'Pengajuan not found'], 404);
    //     }
    //     if ($user && $user->role === 'customer' && $pengajuan->user_id !== $user->id) {
    //         return response()->json(['message' => 'Akses ditolak'], 403);
    //     }
    //     $pengajuanArray = $pengajuan->toArray();
    //     $pengajuanArray['destination'] = $pengajuan->destination ? $pengajuan->destination->location : null;
    //     return response()->json($pengajuanArray, 200);
    // }

    public function show($id)
    {
        $pengajuan = Pengajuan::with(['destination', 'paymentsub'])->find($id);

        if (!$pengajuan) {
            return response()->json(['message' => 'Pengajuan not found'], 404);
        }

        $data = $pengajuan->toArray();
        $data['destination'] = $pengajuan->destination ? $pengajuan->destination->location : null;
        $data['payment_proof'] = $pengajuan->paymentsub ? $pengajuan->paymentsub->path_file : null;

        return response()->json($data, 200);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $pengajuan = Pengajuan::find($id);
        if (!$pengajuan) {
            return response()->json(['message' => 'Pengajuan not found'], 404);
        }
        if ($user && $user->role === 'customer' && $pengajuan->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        $request->validate([
            'institution' => 'sometimes|required|string|max:255',
            'applicant' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'destination_id' => 'sometimes|required|exists:destinations,id',
            'vehicle_id' => 'sometimes|required|exists:vehicles,id',
            'departure_date' => 'sometimes|required|date',
            'return_date' => 'sometimes|required|date|after_or_equal:departure_date',
            'participants' => 'sometimes|required|integer|min:1',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:menunggu_konfirmasi,menunggu_persetujuan,disetujui,dalam_perjalanan,menunggu_pembayaran,menunggu_verifikasi_pembayaran,pembayaran_ditolak,lunas,ditolak',
        ]);
        $pengajuan->update($request->all());
        return response()->json($pengajuan, 200);
    }


    public function destroy($id)
    {
        $user = Auth::user();
        $pengajuan = Pengajuan::find($id);
        if (!$pengajuan) {
            return response()->json(['message' => 'Pengajuan not found'], 404);
        }
        if ($user && $user->role === 'customer' && $pengajuan->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        $pengajuan->delete();
        return response()->json(['message' => 'Pengajuan berhasil dihapus'], 200);
    }

    // public function updateStatus(Request $request, $id)
    // {
    //     // Validate request
    //     $request->validate([
    //         'status' => 'required|string|in:menunggu_konfirmasi,menunggu_persetujuan,disetujui,dalam_perjalanan,menunggu_pembayaran,lunas,ditolak'
    //     ]);

    //     // Find pengajuan
    //     $pengajuan = Pengajuan::findOrFail($id);

    //     // Update status
    //     $pengajuan->status = $request->status;
    //     $pengajuan->save();

    //     return response()->json([
    //         'message' => 'Status pengajuan berhasil diperbarui',
    //         'data' => $pengajuan
    //     ], 200);
    // }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:menunggu_konfirmasi,menunggu_persetujuan,disetujui,dalam_perjalanan,menunggu_pembayaran,menunggu_verifikasi_pembayaran,pembayaran_ditolak,lunas,ditolak,invoice_terkirim'
        ]);

        $pengajuan = Pengajuan::with(['destination', 'paymentsub'])->findOrFail($id);
        $pengajuan->status = $request->status;
        $pengajuan->save();

        // Kirim email invoice jika status invoice_terkirim
        if ($pengajuan->status === 'invoice_terkirim' && $pengajuan->email) {
            $invoice = \App\Models\Invoice::where('pengajuan_id', $pengajuan->id)->latest()->first();
            if ($invoice) {
                Mail::to($pengajuan->email)->send(new \App\Mail\InvoiceMail($invoice));
            }
        }

        // Kirim email bukti pembayaran jika status lunas/pembayaran diterima
        if ($pengajuan->status === 'lunas' && $pengajuan->email) {
            $paymentsub = $pengajuan->paymentsub;
            if ($paymentsub) {
                Mail::to($pengajuan->email)->send(new \App\Mail\PaymentReceivedMail($pengajuan, $paymentsub));
            }
        }

        return response()->json([
            'message' => 'Status pengajuan berhasil diperbarui',
            'data' => $pengajuan
        ], 200);
    }

    public function resendPaymentReceived($id)
    {
        $pengajuan = Pengajuan::with(['destination', 'paymentsub'])->find($id);
        if (!$pengajuan) {
            return response()->json(['message' => 'Pengajuan not found'], 404);
        }
        if (!$pengajuan->email) {
            return response()->json(['message' => 'Customer email not found'], 404);
        }
        $paymentsub = $pengajuan->paymentsub;
        if (!$paymentsub) {
            return response()->json(['message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        Mail::to($pengajuan->email)->send(new \App\Mail\PaymentReceivedMail($pengajuan, $paymentsub));

        return response()->json(['message' => 'Email pembayaran diterima berhasil dikirim ulang ke customer.']);
    }
}
