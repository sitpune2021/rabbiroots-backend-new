<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    // Add to wishlist
    public function add(Request $request)
    {
        try {

            Log::info('Wishlist add request received', [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ]);

            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);

            $user_id = Auth::id();

            $exists = Wishlist::where('user_id', $user_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($exists) {

                Log::warning('Product already exists in wishlist', [
                    'user_id' => $user_id,
                    'product_id' => $request->product_id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Product already in wishlist'
                ]);
            }

            $wishlist = Wishlist::create([
                'user_id' => $user_id,
                'product_id' => $request->product_id
            ]);

            Log::info('Product added to wishlist successfully', [
                'wishlist_id' => $wishlist->id,
                'user_id' => $user_id,
                'product_id' => $request->product_id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product added to wishlist'
            ]);
        } catch (\Exception $e) {

            Log::error('Wishlist add failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }


    // Get wishlist
    public function list()
    {
        $wishlist = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'status' => true,
            'data' => $wishlist
        ]);
    }


    // Remove from wishlist
    public function remove($product_id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product_id)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found in wishlist'
            ]);
        }

        $wishlist->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product removed from wishlist'
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
