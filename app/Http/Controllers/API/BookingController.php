<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        try {
            $bookings = Booking::with(['user', 'package', 'vehicle'])->get();
            return response()->json([
                'status' => 'success',
                'data' => $bookings
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'nullable|exists:packages,id',
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
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Booking berhasil dibuat',
            'data' => $booking
        ], 201);
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'package', 'vehicle'])->find($id);
        return $booking 
            ? response()->json(['data' => $booking], 200) 
            : response()->json(['message' => 'Booking tidak ditemukan'], 404);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $booking->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Status booking berhasil diperbarui',
            'data' => $booking
        ], 200);
    }

    public function destroy($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        $booking->delete();
        return response()->json(['message' => 'Booking berhasil dihapus'], 200);
    }
}
