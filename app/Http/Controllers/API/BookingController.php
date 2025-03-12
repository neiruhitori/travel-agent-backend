<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index() {
        return response()->json(Booking::all(), 200);
    }

    public function store(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:packages,id',
            'booking_date' => 'required|date',
            'total_price' => 'required|numeric',
            'status' => 'required|string'
        ]);

        $booking = Booking::create($request->all());
        return response()->json($booking, 201);
    }

    public function show($id) {
        $booking = Booking::find($id);
        return $booking ? response()->json($booking, 200) : response()->json(['message' => 'Booking not found'], 404);
    }

    public function update(Request $request, $id) {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['message' => 'Booking not found'], 404);

        $booking->update($request->all());
        return response()->json($booking, 200);
    }

    public function destroy($id) {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['message' => 'Booking not found'], 404);

        $booking->delete();
        return response()->json(['message' => 'Booking delete'], 200);
    }
}
