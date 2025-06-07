<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('booking.package')->get();
        return response()->json($payments, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'user_id' => 'required|integer|exists:users,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|string|in:pending,completed,failed',
            'payment_date' => 'required|date',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $path = $file->store('bukti_pembayaran', 'public');
            $data['bukti_pembayaran'] = $path;
        }

        $payment = Payment::create($data);
        return response()->json($payment, 201);
    }

    public function show($id)
    {
        $payment = Payment::with('booking.user', 'booking.package', 'booking.vehicle', 'transaction')->find($id);
        return $payment ? response()->json($payment, 200) : response()->json(['message' => 'Payment not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);
        if (!$payment) return response()->json(['message' => 'Payment not found'], 404);

        $request->validate([
            'payment_method' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'status' => 'sometimes|string|in:pending,completed,failed',
            'payment_date' => 'sometimes|date',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $path = $file->store('bukti_pembayaran', 'public');
            $data['bukti_pembayaran'] = $path;
        }

        $payment->update($data);
        return response()->json($payment, 200);
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);
        if (!$payment) return response()->json(['message' => 'Payment not found'], 404);

        $payment->delete();
        return response()->json(['message' => 'Payment delete'], 200);
    }
}
