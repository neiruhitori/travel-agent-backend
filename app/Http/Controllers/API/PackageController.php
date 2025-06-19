<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('destination:id,location,name,image')
            ->get()
            ->map(function ($package) {
                $package->image_url = $package->image
                    ? asset('storage/' . $package->image)
                    : null;
                // Tambahkan image_url untuk destination juga
                if ($package->destination) {
                    $package->destination->image_url = $package->destination->image
                        ? asset('storage/' . $package->destination->image)
                        : null;
                }
                return $package;
            });

        return response()->json([
            'packages' => $packages
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'destination_id' => 'required|exists:destinations,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        try {
            $data = $request->all();

            if ($request->hasFile('image')) {
                $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
                Storage::disk('public')->put($imageName, file_get_contents($request->image));
                $data['image'] = $imageName;
            }

            $package = Package::create($data);
            return response()->json([
                'status' => 'success',
                'message' => 'Package berhasil dibuat',
                'data' => $package
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat package'
            ], 500);
        }
    }

    public function show($id)
    {
        $package = Package::with('destination')->find($id);
        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }
        $package->image_url = $package->image
            ? asset('storage/' . $package->image)
            : null;
        if ($package->destination) {
            $package->destination->image_url = $package->destination->image
                ? asset('storage/' . $package->destination->image)
                : null;
        }
        return response()->json($package, 200);
    }

    public function update(Request $request, $id)
    {
        $package = Package::find($id);
        if (!$package) return response()->json(['message' => 'Package not found'], 404);

        try {
            $data = $request->all();

            if ($request->hasFile('image')) {
                $storage = Storage::disk('public');
                if ($storage->exists($package->image)) {
                    $storage->delete($package->image);
                }
                $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
                $storage->put($imageName, file_get_contents($request->image));
                $data['image'] = $imageName;
            }

            $package->update($data);

            // Tambahkan image_url di response update
            $package->image_url = $package->image
                ? asset('storage/' . $package->image)
                : null;

            return response()->json([
                'status' => 'success',
                'message' => 'Package berhasil diupdate',
                'data' => $package
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate package'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $package = Package::find($id);
            if (!$package) return response()->json(['message' => 'Package not found'], 404);

            // Delete image from storage if exists
            if ($package->image) {
                $storage = Storage::disk('public');
                if ($storage->exists($package->image)) {
                    $storage->delete($package->image);
                }
            }

            $package->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Package berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus package'
            ], 500);
        }
    }

    // public function search(Request $request)
    // {
    //     $request->validate([
    //         'destination_id' => 'required|integer',
    //     ]);

    //     $packages = Package::where('destination_id', $request->destination_id)
    //         ->where('status', 'active')
    //         ->get();

    //     return response()->json([
    //         'available' => $packages->isNotEmpty(),
    //         'packages' => $packages,
    //     ]);
    // }
}
