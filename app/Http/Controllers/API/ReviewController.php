<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index() {
        return response()->json(Review::all(), 200);
    }

    public function store(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'package_id' => 'required|exists:packages,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string'
        ]);

        $review = Review::create($request->all());
        return response()->json($review, 201);
    }

    public function show($id) {
        $review = Review::find($id);
        return $review ? response()->json($review, 200) : response()->json(['message' => 'Review not found'], 404);
    }

    public function update(Request $request, $id) {
        $review = Review::find($id);
        if (!$review) return response()->json(['message' => 'Review not found'], 404);

        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string'
        ]);

        $review->update($request->all());
        return response()->json($review, 200);
    }

    public function destroy($id) {
        $review = Review::find($id);
        if (!$review) return response()->json(['message' => 'Review not found'], 404);

        $review->delete();
        return response()->json(['message' => 'Review delete'], 200);
    }

}
