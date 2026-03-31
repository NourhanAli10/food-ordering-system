<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponsesTrait;


    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $cartItems =  Cart::with('product')->where('user_id', $userId)->get();

        $total = 0;
        $items = [];

        foreach ($cartItems as $item) {
            $price = $item->product->discount_price ?? $item->product->price;
            $totalPrice = $price * $item->quantity;
            $total += $totalPrice;

            $items[] = [
                'id' => $item->id,
                'product' => $item->product,
                'quantity' => $item->quantity,
                'total_price' => $totalPrice,
                'total' => $total
            ];
        }

        return $this->successResponse(
            message: "product saved successfully",
            data: [
                'cart' => $items
            ]

        );
    }

    /**
     * Add an item to the cart
     */

    public function store(Request $request)
    {
        $validated =  $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        $validated['user_id'] = $request->user()->id;

        $cartItem = Cart::where('user_id', $validated['user_id'])
            ->where('product_id', $validated['product_id'])->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $validated['quantity'],
            ]);
        } else {
            $cartItem = Cart::create($validated);
        }


        return $this->successResponse(
            message: "product saved successfully",
            data: [
                'cart' => $cartItem
            ]
        );
    }

    /**
     * Update cart item
     */

    public function update(Request $request, string $id)
    {
        $validated =  $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        $userId = $request->user()->id;
        $cartItem = Cart::where('user_id', $userId)->findOrFail($id);
        if (!$cartItem) {
            return $this->errorResponse(
                message: "Cart item not found"
            );
        }
        $cartItem->update($validated);

        return $this->successResponse(
            message: "product updated successfully",
            data: [
                $cartItem
            ]
        );
    }


    /**
     * Remove item from cart
     */

    public function destroy(Request $request, string $id)
    {
        $userId = $request->user()->id;
        $cartItem = Cart::where('user_id', $userId)->findOrFail($id);
        if (!$cartItem) {
            return $this->errorResponse(
                message: "Cart item not found"
            );
        }
        $cartItem->delete();
        return $this->successResponse(
            message: "product deleted successfully",
        );
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        $userId = $request->user()->id;
        Cart::where('user_id', $userId)->delete();
        return $this->successResponse(
            message: "cart cleared successfully",
        );
    }
}
