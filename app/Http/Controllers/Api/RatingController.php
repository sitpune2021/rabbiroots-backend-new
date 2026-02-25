<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{

    // Give Rating API (Customer → Agent)
    public function giveReview(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'agent_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        $customer = auth()->user();

        // Prevent duplicate review
        $exists = Review::where('order_id', $request->order_id)
            ->where('customer_id', $customer->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'You already reviewed this order'
            ], 400);
        }

        $review = Review::create([
            'order_id' => $request->order_id,
            'customer_id' => $customer->id,
            'agent_id' => $request->agent_id,
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        // Update average rating
        $avgRating = Review::where('agent_id', $request->agent_id)->avg('rating');

        DB::table('delivery_agents')
            ->where('user_id', $request->agent_id)
            ->update(['rating_avg' => round($avgRating, 2)]);

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully',
            'data' => $review
        ]);
    }


    // Get Agent Reviews API
    public function agentReviews()
    {
        $agent = auth()->user();

        $reviews = Review::where('agent_id', $agent->id)
            ->with('customer:id,name')
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'total_reviews' => Review::where('agent_id', $agent->id)->count(),
            'average_rating' => round(
                Review::where('agent_id', $agent->id)->avg('rating'),
                2
            ),
            'data' => $reviews
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
