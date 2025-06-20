<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    // List all invoices
    public function index()
    {
        $invoices = Invoice::with(['pengajuan', 'user'])->get();
        return response()->json($invoices, 200);
    }

    // Store a new invoice
    public function store(Request $request)
    {
        $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan,id',
            'total' => 'required|numeric|min:0',
        ]);

        $pengajuan = Pengajuan::find($request->pengajuan_id);

        // Ambil user_id dari pengajuan
        $user_id = $pengajuan->user_id;

        $invoice = Invoice::create([
            'pengajuan_id' => $request->pengajuan_id,
            'user_id' => $user_id,
            'total' => $request->total,
            'status' => 'sent', // default status
        ]);

        // Kirim email ke customer setelah invoice dibuat
        if ($pengajuan->email) {
            Mail::to($pengajuan->email)->send(new \App\Mail\InvoiceMail($invoice));
        }

        return response()->json([
            'message' => 'Invoice berhasil dibuat',
            'invoice' => $invoice
        ], 201);
    }

    // Show detail invoice
    public function show($id)
    {
        $invoice = Invoice::with(['pengajuan', 'user'])->find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        return response()->json($invoice, 200);
    }

    // Update invoice (opsional)
    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $request->validate([
            'total' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string',
        ]);
        $invoice->update($request->all());
        return response()->json($invoice, 200);
    }

    // Delete invoice (opsional)
    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $invoice->delete();
        return response()->json(['message' => 'Invoice berhasil dihapus'], 200);
    }

    // Ambil invoice berdasarkan pengajuan_id
    public function byPengajuan($pengajuan_id)
    {
        $invoice = Invoice::with(['pengajuan', 'user'])->where('pengajuan_id', $pengajuan_id)->first();
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        // Tambahkan alias total_harga agar frontend tidak bingung
        $data = $invoice->toArray();
        $data['total_harga'] = $invoice->total;
        return response()->json($data, 200);
    }

    public function resendEmail($id)
    {
        $invoice = Invoice::with('pengajuan')->find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $customerEmail = $invoice->pengajuan->email;
        if (!$customerEmail) {
            return response()->json(['message' => 'Customer email not found'], 404);
        }

        // Kirim email (gunakan Mailable sesuai kebutuhan)
        Mail::to($customerEmail)->send(new \App\Mail\InvoiceMail($invoice));

        return response()->json(['message' => 'Invoice email resent to customer.']);
    }
}
