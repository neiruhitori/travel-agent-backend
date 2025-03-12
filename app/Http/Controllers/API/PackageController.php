<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index() {
        return response()->json(Package::all(), 200);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'destination_id' => 'required|exists:destinations,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'required|string',
            'image' => 'nullable|string'
        ]);

        $package = Package::create($request->all());
        return response()->json($package, 201);
    }

    public function show($id) {
        $package = Package::find($id);
        return $package ? response()->json($package, 200) : response()->json(['message' => 'Package not found'], 404);
    }

    public function update(Request $request, $id) {
        $package = Package::find($id);
        if (!$package) return response()->json(['message' => 'Package not found'], 404);

        $package->update($request->all());
        return response()->json($package, 200);
    }

    public function destroy($id) {
        $package = Package::find($id);
        if (!$package) return response()->json(['message' => 'Package not found'], 404);

        $package->delete();
        return response()->json(['message' => 'Package delete'], 200);
    }
}
