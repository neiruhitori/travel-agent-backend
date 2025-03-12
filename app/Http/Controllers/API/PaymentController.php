<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index() {
        return response()->json(Payment::all(), 200);
    }

    public function store(Request $request) {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|string|in:pending,completed,failed',
            'payment_date' => 'required|date'
        ]);

        $payment = Payment::create($request->all());
        return response()->json($payment, 201);
    }

    public function show($id) {
        $payment = Payment::find($id);
        return $payment ? response()->json($payment, 200) : response()->json(['message' => 'Payment not found'], 404);
    }

    public function update(Request $request, $id) {
        $payment = Payment::find($id);
        if (!$payment) return response()->json(['message' => 'Payment not found'], 404);

        $request->validate([
            'payment_method' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'status' => 'sometimes|string|in:pending,completed,failed',
            'payment_date' => 'sometimes|date'
        ]);

        $payment->update($request->all());
        return response()->json($payment, 200);
    }

    public function destroy($id) {
        $payment = Payment::find($id);
        if (!$payment) return response()->json(['message' => 'Payment not found'], 404);

        $payment->delete();
        return response()->json(['message' => 'Payment delete'], 200);
    }

}
