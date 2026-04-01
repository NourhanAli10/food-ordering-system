<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CouponService;
use App\Services\OrderService;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class OrderController extends Controller
{


    use ApiResponsesTrait;

    public OrderService $orderService;
    public CouponService $couponservice;

    public function __construct(OrderService $orderService, CouponService $couponservice)
    {
        $this->orderService = $orderService;
        $this->couponservice = $couponservice;
    }


    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('orderItems', 'orderItems.product')
            ->latest()->get();
        return $this->successResponse(data: ['orders' => $orders]);
    }


    /**
     * Create a new order
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'name' => 'required|string|min:2|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:12',
            'second_phone' => 'nullable|string|max:12',
            'address' => 'required|string|max:255',
            'building_number' => 'required|string|max:255',
            'apartment' => 'required|string|max:20',
            'floor' => 'required|string|max:20',
            'area_id' => 'required|exists:areas,id',
            'city' => 'required|string|max:100',
            'type' => 'required|in:delivery,pickup,dine_in',
            'notes' => 'nullable|string',
            'payment_method' => 'required|in:cash,card',
        ]);

        $userId = $request->user()->id;

        $cartItems = Cart::with('product')->where('user_id', $userId)->get();
        if ($cartItems->isEmpty()) {
            return $this->errorResponse(message: 'Cart is empty');
        }

        try {
            $order = DB::transaction(function () use ($userId, $validated, $cartItems) {
                $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . uniqid();

                $subtotal = 0;

                foreach ($cartItems as $item) {
                    $price = $item->product->price * $item->quantity;
                    $subtotal += $price;
                }

                $discount = 0;

                $coupon = null;


                if (!empty($validated['coupon_code'])) {
                    $coupon = $this->couponservice->validateCoupon($validated['coupon_code'], $userId, $subtotal);
                    $discount = $this->couponservice->calculateDiscount($coupon, $subtotal);
                }


                $deliveryFee = $validated['type'] == 'delivery' ? $this->orderService->calculateDelivery($validated['area_id']) : 0;


                $vat = ($subtotal - $discount) * 0.14;

                $total = $subtotal - $discount + $vat + $deliveryFee;

                $order = Order::create([
                    'order_number' =>  $orderNumber,
                    'user_id' => $userId,
                    'coupon_id' => $coupon?->id,
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'second_phone' => $validated['second_phone'],
                    'area_id' => $validated['area_id'],
                    'email' => $validated['email'],
                    'address' => $validated['address'],
                    'floor' => $validated['floor'],
                    'building_number' => $validated['building_number'],
                    'apartment' => $validated['apartment'],
                    'city' => $validated['city'],
                    'type' => $validated['type'],
                    'status' => 'pending',
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'pending',
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $vat,
                    'delivery_fee' => $deliveryFee,
                    'total' => $total,
                    'notes' => $validated['notes'],
                ]);

                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'user_id' => $userId,
                        'name' => $item->product->name,
                        'product_id' => $item->product->id,
                        'price' => $item->product->price,
                        'quantity' => $item->quantity,
                        'total' => $item->product->price * $item->quantity,
                    ]);
                }
                $cartItems->each->delete();

                return $order;
            });
            return $this->successResponse(
                message: 'order created successfully',
                data: [
                    'data' =>  $order
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(message: $e->getMessage());
        }
    }
}
