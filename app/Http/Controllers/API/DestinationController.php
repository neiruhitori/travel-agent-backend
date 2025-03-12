<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    public function index()
    {
        return response()->json(Destination::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'location', 'description', 'price']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            $data['image'] = Storage::url($imagePath);
        }

        $destination = Destination::create($data);

        return response()->json([
            'message' => 'Data berhasil ditambahkan!',
            'data' => $destination
        ], 201);
    }

    public function show($id)
    {
        $destination = Destination::find($id);
        return $destination ? response()->json($destination, 200) : response()->json(['message' => 'Destination not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $destination = Destination::find($id);
        if (!$destination) {
            return response()->json(['message' => 'Destination tidak ditemukan'], 404);
        }

        $destination->update($request->all());

        return response()->json([
            'message' => 'Data berhasil diperbarui!',
            'data' => $destination
        ], 200);
    }

    public function destroy($id)
    {
        $destination = Destination::find($id);
        if (!$destination) return response()->json(['message' => 'Destination tidak ditemukan'], 404);

        $destination->delete();
        return response()->json(['message' => 'Destination berhasil dihapus'], 200);
    }
}
