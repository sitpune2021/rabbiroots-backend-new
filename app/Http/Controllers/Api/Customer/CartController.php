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

            // ✅ VALIDATION
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1'
            ]);

            $customer_id = Auth::id();

            $responseData = [];

            foreach ($request->items as $item) {

                $product = Product::find($item['product_id']);

                if (!$product) {
                    continue; // skip invalid product
                }

                // ❗ STOCK CHECK
                if ($product->stock < $item['quantity']) {

                    $responseData[] = [
                        'product_id' => $product->id,
                        'status' => false,
                        'message' => 'Out of stock'
                    ];
                    continue;
                }

                // CHECK EXISTING CART
                $cart = Cart::where('customer_id', $customer_id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($cart) {

                    $cart->quantity += $item['quantity'];
                    $cart->save();

                    $responseData[] = [
                        'product_id' => $product->id,
                        'status' => true,
                        'message' => 'Quantity updated',
                        'data' => $cart
                    ];
                } else {

                    $cart = Cart::create([
                        'customer_id' => $customer_id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price
                    ]);

                    $responseData[] = [
                        'product_id' => $product->id,
                        'status' => true,
                        'message' => 'Added to cart',
                        'data' => $cart
                    ];
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Cart processed',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {

            Log::error('Add to cart failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    // update cart quantity api
    public function updateQuantity(Request $request)
    {
        try {

            // ✅ Validation
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'action' => 'required|in:increment,decrement'
            ]);

            $customer_id = auth()->id();

            // ✅ Get cart item
            $cart = Cart::where('customer_id', $customer_id)
                ->where('product_id', $request->product_id)
                ->first();

            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            // ✅ Get product
            $product = Product::find($request->product_id);

            // ➕ INCREMENT
            if ($request->action === 'increment') {

                if ($product->stock <= $cart->quantity) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Stock limit reached'
                    ]);
                }

                $cart->quantity += 1;
            }

            // ➖ DECREMENT
            if ($request->action === 'decrement') {

                if ($cart->quantity <= 1) {

                    $cart->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Item removed from cart'
                    ]);
                }

                $cart->quantity -= 1;
            }

            $cart->save();

            return response()->json([
                'status' => true,
                'message' => 'Cart updated successfully',
                'data' => [
                    'product_id' => $cart->product_id,
                    'quantity'   => $cart->quantity,
                    'price'      => $cart->price
                ]
            ]);
        } catch (\Exception $e) {

            Log::error('Cart update error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
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
