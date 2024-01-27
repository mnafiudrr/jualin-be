<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use HttpResponses;

    /**
     * Get all product in cart.
     */
    public function index()
    {
        $cart = Cart::getCart();

        if (!$cart)
            return $this->errorResponse('Cart not found', 'Not Found', 404);

        return $this->successResponse(new CartResource($cart), 'Cart retrieved successfully');
    }

    /**
     * Add product to cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);
        try {
            $cart = Cart::storeProduct($request->all());

            if (!$cart)
                return $this->errorResponse('Product stock is empty', 'Not Found', 404);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Server Error.',
                'errors' => $th->getMessage(),
            ], 500);
        }

        return $this->successResponse(new CartResource($cart), 'Product added to cart successfully', 201);
    }

    /**
     * Delete product in cart.
     */
    public function delete(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $cart = Cart::deleteProduct($request->all());

            if (!$cart)
                return $this->errorResponse('Cart not found', 'Not Found', 404);
            else if ($cart === true)
                return $this->successResponse(null, 'Cart deleted successfully');
            else
                return $this->successResponse(new CartResource($cart), 'Product deleted successfully');

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Server Error.',
                'errors' => $th->getMessage(),
            ], 500);
        }


    }

}
