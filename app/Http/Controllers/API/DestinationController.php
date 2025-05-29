<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestinationStoreRequest;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::all()->map(function ($destination) {
            // Tambahkan image_url untuk setiap destinasi
            $destination->image_url = $destination->image
                ? asset('storage/' . $destination->image)
                : null;
            return $destination;
        });

        return response()->json([
            'destinations' => $destinations
        ], 200);
    }

    public function store(DestinationStoreRequest $request)
    {
        try {
            $name = $request->name;
            $location = $request->location;
            $description = $request->description;
            $price = $request->price;
            $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();

            Storage::disk('public')->put($imageName, file_get_contents($request->image));

            Destination::create([
                'name' => $name,
                'location' => $location,
                'description' => $description,
                'price' => $price,
                'image' => $imageName
            ]);

            return response()->json([
                'results' => "Destination Successfully created. '$name' -- '$location' -- '$description' -- '$price' -- '$imageName' "
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    public function show($id)
    {
        $destination = Destination::find($id);
        if (!$destination) {
            return response()->json(['message' => 'Destination Not Found.'], 404);
        }

        return response()->json([
            'destination' => $destination
        ], 200);
    }

    public function update(DestinationStoreRequest $request, $id)
    {
        try {
            $destination = Destination::find($id);
            if (!$destination) {
                return response()->json([
                    'message' => 'Destination Not Found.'
                ], 404);
            }

            echo "request : $request->image";
            $destination->name = $request->name;
            $destination->location = $request->location;
            $destination->description = $request->description;
            $destination->price = $request->price;

            if ($request->image) {
                $storage = Storage::disk('public');

                // Hapus gambar sebelumnya jika ada
                if ($storage->exists($destination->image))
                    $storage->delete($destination->image);

                $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
                $destination->image = $imageName;

                // Simpan gambar di folder
                $storage->put($imageName, file_get_contents($request->image));
            }

            $destination->save();

            return response()->json([
                'message' => "Destination successfully update."
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $destination = Destination::find($id);
            if (!$destination) return response()->json(['message' => 'Destination not found'], 404);

            if ($destination->image) {
                $storage = Storage::disk('public');
                if ($storage->exists($destination->image)) {
                    $storage->delete($destination->image);
                }
            }

            $destination->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Destination successfully delete.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete destination!'
            ], 500);
        }
    }
}
