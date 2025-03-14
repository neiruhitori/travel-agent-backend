<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index() {
        return response()->json(Transaction::all(), 200);
    }

    public function store(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'payment_id' => 'required|exists:payments,id',
            'transaction_date' => 'required|date',
            'status' => 'required|string|in:pending,success,failed'
        ]);

        $transaction = Transaction::create($request->all());
        return response()->json($transaction, 201);
    }

    public function show($id) {
        $transaction = Transaction::find($id);
        return $transaction ? response()->json($transaction, 200) : response()->json(['message' => 'Transaction not found'], 404);
    }

    public function update(Request $request, $id) {
        $transaction = Transaction::find($id);
        if (!$transaction) return response()->json(['message' => 'Transaction not found'], 404);

        $request->validate([
            'transaction_date' => 'sometimes|date',
            'status' => 'sometimes|string|in:pending,success,failed'
        ]);

        $transaction->update($request->all());
        return response()->json($transaction, 200);
    }

    public function destroy($id) {
        $transaction = Transaction::find($id);
        if (!$transaction) return response()->json(['message' => 'Transaction not found'], 404);

        $transaction->delete();
        return response()->json(['message' => 'Transaction delete'], 200);
    }
}
