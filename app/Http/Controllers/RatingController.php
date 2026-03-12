<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderRating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Store a new rating for an order (public — from tracking page)
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:500',
        ]);

        $order = Order::where('order_code', $request->order_code)->first();

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        if ($order->status !== 'Diambil') {
            return response()->json(['message' => 'Rating hanya bisa diberikan setelah cucian diambil.'], 422);
        }

        // Prevent duplicate ratings
        if ($order->rating()->exists()) {
            return response()->json(['message' => 'Anda sudah memberikan rating untuk order ini.'], 409);
        }

        $rating = OrderRating::create([
            'order_id'      => $order->id,
            'rating'        => $request->rating,
            'comment'       => $request->comment,
            'reviewer_name' => $order->customer_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas penilaian Anda! ⭐',
            'data'    => $rating,
        ]);
    }
}
