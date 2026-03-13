<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        try {

            Log::info('Add to cart request received', [
                'customer_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $customer_id = Auth::id();

            $product = Product::find($request->product_id);

            if (!$product) {

                Log::warning('Product not found while adding to cart', [
                    'product_id' => $request->product_id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // check stock
            if ($product->stock < $request->quantity) {

                Log::warning('Product out of stock', [
                    'product_id' => $product->id,
                    'available_stock' => $product->stock,
                    'requested_qty' => $request->quantity
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Product out of stock'
                ]);
            }

            // check if product already in cart
            $cart = Cart::where('customer_id', $customer_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($cart) {

                $cart->quantity = $cart->quantity + $request->quantity;
                $cart->save();

                Log::info('Cart quantity updated', [
                    'cart_id' => $cart->id,
                    'customer_id' => $customer_id,
                    'product_id' => $product->id,
                    'new_quantity' => $cart->quantity
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Cart quantity updated',
                    'data' => $cart
                ]);
            } else {

                $cart = Cart::create([
                    'customer_id' => $customer_id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'price' => $product->price
                ]);

                Log::info('Product added to cart successfully', [
                    'cart_id' => $cart->id,
                    'customer_id' => $customer_id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Product added to cart',
                    'data' => $cart
                ]);
            }
        } catch (\Exception $e) {

            Log::error('Add to cart failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'customer_id' => Auth::id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while adding to cart'
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
