<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartOfferChoices;
use App\Models\Offer;
use App\Models\Product;
use App\Services\OfferService;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    use ApiResponsesTrait;

    public OfferService $offerService;


    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }


    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $cartItems =  Cart::with('product', 'offer', 'choices.product')
            ->where('user_id', $userId)->get();
        // $total = 0;
        // $items = [];

        // foreach ($cartItems as $item) {
        //     dd($item);
        //     if (!empty($item->offer_id) && empty($item->product_id)) {
        //         $totalPrice = $item->price * $item->quantity;
        //         $total += $totalPrice;

        //         $items[] = [
        //             'id' => 
        //         ]
        //     } else {
        //         $price = $item->product->discount_price ?? $item->product->price;
        //         $totalPrice = $price * $item->quantity;
        //         $total += $totalPrice;

        //         $items[] = [
        //             'id' => $item->id,
        //             'product' => $item->product,
        //             'quantity' => $item->quantity,
        //             'total_price' => $totalPrice,
        //             'total' => $total
        //         ];
        //     }
        // }

        return $this->successResponse(
            message: "product saved successfully",
            data: [
                'cart' => $cartItems
            ]

        );
    }

    /**
     * Add an item to the cart
     */

    public function store(Request $request)
    {
        $validated =  $request->validate([
            'product_id' => 'nullable|exists:products,id|required_without:offer_id',
            'quantity' => 'required|integer|min:1',
            'offer_id' => 'nullable|exists:offers,id|required_without:product_id',
            'choices' => 'required_with:offer_id|array|min:1',
            'choices.*.offer_group_id' => 'required_with:offer_id|exists:offer_groups,id',
            'choices.*.products' => 'required_with:offer_id|array|min:1',
            'choices.*.products.*' => 'required_with:offer_id|exists:products,id'
        ]);


        $validated['user_id'] = $request->user()->id;

        $price = 0;
        $cartItem = null;

        if (!empty($validated['product_id'])) {
            $product = Product::findOrFail($validated['product_id']);
            $price = $product->discount_price ?? $product->price;
            $cartItem = Cart::where('user_id', $validated['user_id'])
                ->where('product_id', $validated['product_id'])->first();
        }

        if (!empty($validated['offer_id'])) {
            $offer = Offer::with('groups.products')->findOrFail($validated['offer_id']);
            $price = $this->offerService->applyToProduct($offer, $validated['choices']);
            $cartItem = Cart::where('user_id', $validated['user_id'])
                ->where('offer_id', $validated['offer_id'])->first();
        }

        $validated['price'] = $price;




        if ($cartItem) {
            $cartItem->increment('quantity', $validated['quantity']);
        } else {
            $cartItem = Cart::create(Arr::except($validated, ['choices']));

            if (!empty($validated['offer_id'])) {
                foreach ($validated['choices'] as $choice) {
                    foreach ($choice['products'] as $productId)
                        CartOfferChoices::create([
                            'cart_id' => $cartItem->id,
                            'offer_group_id' => $choice['offer_group_id'],
                            'product_id' => $productId,
                        ]);
                }
            }
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
