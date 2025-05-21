<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;
use App\Models\Destination;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;

class PengajuanController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if ($user && $user->role === 'customer') {
            $pengajuan = Pengajuan::where('user_id', $user->id)->get();
        } else {
            $pengajuan = Pengajuan::all();
        }
        return response()->json($pengajuan, 200);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'institution' => 'required|string|max:255',
            'applicant' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'participants' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $pengajuan = Pengajuan::create([
            'user_id' => $request->user_id,
            'institution' => $request->institution,
            'applicant' => $request->applicant,
            'email' => $request->email,
            'destination_id' => $request->destination_id,
            'vehicle_id' => $request->vehicle_id,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'participants' => $request->participants,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Pengajuan berhasil disimpan', 'pengajuan' => $pengajuan], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $pengajuan = Pengajuan::find($id);
        if (!$pengajuan) {
            return response()->json(['message' => 'Pengajuan not found'], 404);
        }
        if ($user && $user->role === 'customer' && $pengajuan->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        return response()->json($pengajuan, 200);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $pengajuan = Pengajuan::find($id);
        if (!$pengajuan) {
            return response()->json(['message' => 'Pengajuan not found'], 404);
        }
        if ($user && $user->role === 'customer' && $pengajuan->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        $request->validate([
            'institution' => 'sometimes|required|string|max:255',
            'applicant' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'destination_id' => 'sometimes|required|exists:destinations,id',
            'vehicle_id' => 'sometimes|required|exists:vehicles,id',
            'departure_date' => 'sometimes|required|date',
            'return_date' => 'sometimes|required|date|after_or_equal:departure_date',
            'participants' => 'sometimes|required|integer|min:1',
            'notes' => 'nullable|string',
        ]);
        $pengajuan->update($request->all());
        return response()->json($pengajuan, 200);
    }


    public function destroy($id)
    {
        $user = Auth::user();
        $pengajuan = Pengajuan::find($id);
        if (!$pengajuan) {
            return response()->json(['message' => 'Pengajuan not found'], 404);
        }
        if ($user && $user->role === 'customer' && $pengajuan->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        $pengajuan->delete();
        return response()->json(['message' => 'Pengajuan berhasil dihapus'], 200);
    }
}
