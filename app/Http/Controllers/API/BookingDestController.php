<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookingdes;
use Illuminate\Http\Request;

class BookingDestController extends Controller
{
    public function index()
    {
        $bookings = Bookingdes::with(['user', 'destination', 'vehicle'])->get();
        return response()->json($bookings, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'destination_id' => 'required|exists:destinations,id', // Ini membuat destination_id optional
            'vehicle_id' => 'required|exists:vehicles,id',
            'booking_date' => 'required|date',
            'jumlah_penumpang' => 'required|numeric|min:1',
            'total_price' => 'required|numeric|min:0',
        ]);

        $booking = Bookingdes::create([
            'user_id' => $request->user_id,
            'destination_id' => $request->destination_id,
            'vehicle_id' => $request->vehicle_id,
            'booking_date' => $request->booking_date,
            'jumlah_penumpang' => $request->jumlah_penumpang,
            'total_price' => $request->total_price,
            'status' => 'pending', // default
        ]);

        return response()->json(['message' => 'Booking berhasil disimpan', 'booking' => $booking], 201);
    }

    public function show($id)
    {
        $booking = Bookingdes::find($id);
        return $booking ? response()->json($booking, 200) : response()->json(['message' => 'Booking not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $booking = Bookingdes::find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'destination_id' => 'required|exists:destinations,id', // Ini membuat destination_id optional
            'vehicle_id' => 'required|exists:vehicles,id',
            'booking_date' => 'required|date',
            'jumlah_penumpang' => 'required|numeric|min:1',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,cancelled,confirmed,completed',
        ]);

        $booking->update($request->all());
        return response()->json(['message' => 'Booking updated successfully', 'data' => $booking], 200);
    }

    public function destroy($id)
    {
        $booking = Bookingdes::find($id);
        if (!$booking) return response()->json(['message' => 'Booking not found'], 404);

        $booking->delete();
        return response()->json(['message' => 'Booking delete'], 200);
    }
}
