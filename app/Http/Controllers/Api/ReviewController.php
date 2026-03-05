<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    // Give Rating API (Customer → Agent)
    public function giveReview(Request $request)
    {
        try {

            $customer = auth()->user();

            Log::info('Review API called', [
                'user_id' => auth()->id(),
                'role_id' => $customer?->role_id,
                'request_data' => $request->all()
            ]);

            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // ✅ Allow only customers
            if ($customer->role_id != 4) {
                Log::warning('Non-customer trying to submit review', [
                    'user_id' => $customer->id,
                    'role_id' => $customer->role_id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Only customers can submit reviews'
                ], 403);
            }

            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'agent_id' => 'required|exists:users,id',
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string'
            ]);

            // Optional but Recommended:
            // ✅ Check if this order belongs to this customer
            $order = Order::where('id', $request->order_id)
                ->where('customer_id', $customer->id)
                ->where('agent_id', $request->agent_id)
                ->where('status', 'delivered') // allow review only after delivery
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid order or not eligible for review'
                ], 400);
            }

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

            DB::beginTransaction();

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
                ->update([
                    'rating_avg' => round($avgRating, 2)
                ]);

            DB::commit();

            Log::info('Review submitted successfully', [
                'review_id' => $review->id,
                'agent_id' => $request->agent_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Review submitted successfully',
                'data' => $review
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Review submission failed', [
                'error_message' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    // Get Agent Reviews API
    public function agentReviews()
    {
        try {

            $agent = auth()->user();

            Log::info('Agent reviews API called', [
                'user_id' => $agent?->id,
                'role_id' => $agent?->role_id
            ]);

            if (!$agent) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // ✅ Allow only delivery agents (change role_id accordingly)
            if ($agent->role_id != 3) {
                Log::warning('Non-agent trying to access agent reviews', [
                    'user_id' => $agent->id,
                    'role_id' => $agent->role_id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Only delivery agents can access this.'
                ], 403);
            }

            $reviewsQuery = Review::where('agent_id', $agent->id);

            $totalReviews = $reviewsQuery->count();
            $averageRating = $reviewsQuery->avg('rating');

            $reviews = Review::where('agent_id', $agent->id)
                ->with('customer:id,name')
                ->latest()
                ->paginate(10);

            Log::info('Agent reviews fetched successfully', [
                'agent_id' => $agent->id,
                'total_reviews' => $totalReviews,
                'average_rating' => $averageRating
            ]);

            return response()->json([
                'status' => true,
                'total_reviews' => $totalReviews,
                'average_rating' => $averageRating ? round($averageRating, 2) : 0,
                'data' => $reviews
            ]);
        } catch (\Exception $e) {

            Log::error('Failed to fetch agent reviews', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
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
