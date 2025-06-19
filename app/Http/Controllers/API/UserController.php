<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'role' => 'required|string'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::find($id);
        return $user ? response()->json($user, 200) : response()->json(['message' => 'User not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->only(['name', 'email', 'phone', 'address', 'role']);
        $updatedFields = [];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        foreach ($data as $key => $value) {
            if ($user->$key !== $value) {
                $updatedFields[] = $key;
            }
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully',
            'updated_fields' => $updatedFields,
            'user' => $user
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();
        return response()->json(['message' => 'User delete'], 200);
    }
}
