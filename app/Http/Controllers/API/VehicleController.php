<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index()
    {
        return response()->json(Vehicle::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'license_plate' => 'required|string|max:20|unique:vehicles',
            'status' => 'required|string|in:' . implode(',', Vehicle::STATUSES),
            'description' => 'nullable|string',
        ]);

        $vehicle = Vehicle::create($request->all());

        return response()->json([
            'message' => 'Vehicle berhasil ditambahkan!',
            'data' => $vehicle
        ], 201);
    }

    public function show($id)
    {
        $vehicle = Vehicle::find($id);
        return $vehicle ? response()->json($vehicle, 200) : response()->json(['message' => 'Vehicle not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle tidak ditemukan'], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $id,
            'status' => 'required|string|in:' . implode(',', Vehicle::STATUSES),
            'description' => 'nullable|string',
        ]);

        $vehicle->update($request->all());

        return response()->json([
            'message' => 'Vehicle berhasil diperbarui!',
            'data' => $vehicle
        ], 200);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) return response()->json(['message' => 'Vehicle tidak ditemukan'], 404);

        $vehicle->delete();
        return response()->json(['message' => 'Vehicle berhasil dihapus'], 200);
    }
}
