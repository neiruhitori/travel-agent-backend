<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        return response()->json(Booking::all(), 200);
    }

    // public function store(Request $request) {
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'package_id' => 'required|exists:packages,id',
    //         'booking_date' => 'required|date',
    //         'jumlah_penumpang' => 'required',
    //         'total_price' => 'required|numeric',
    //         'status' => 'required|string'
    //     ]);

    //     $booking = Booking::create($request->all());
    //     return response()->json($booking, 201);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:packages,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'booking_date' => 'required|date',
            'jumlah_penumpang' => 'required|numeric|min:1',
            'total_price' => 'required|numeric|min:0',
        ]);

        $booking = Booking::create([
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
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
        $booking = Booking::find($id);
        return $booking ? response()->json($booking, 200) : response()->json(['message' => 'Booking not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:packages,id',
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
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['message' => 'Booking not found'], 404);

        $booking->delete();
        return response()->json(['message' => 'Booking delete'], 200);
    }
}
