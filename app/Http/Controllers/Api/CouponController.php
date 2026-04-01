<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\OrderService;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;

class CouponController extends Controller
{

    use ApiResponsesTrait;


    public OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    public function index()
    {
        $coupons = Coupon::latest('created_by')->paginate(10);
        return $this->successResponse(
            data: ['coupons' => $coupons]
        );
    }



    public function show(string $id)
    {
        $coupon = Coupon::findOrFail($id);
        return $this->successResponse(
            data: ['coupon' => $coupon]
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'required|string',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,expired',
            'start_date' => 'required|date',
            'expire_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'max_usage_per_user' => 'nullable|integer|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
        ]);

        $coupon = Coupon::create($validated);

        return $this->successResponse(
            message: "coupon created successfully",
            data: ['coupon' => $coupon]
        );
    }

    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            'code' => 'sometimes|string|max:50|unique:coupons,code,' . $id,
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:fixed,percentage',
            'value' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:active,inactive,expired',
            'start_date' => 'sometimes|date',
            'expire_date' => 'sometimes|date|after:start_date',
            'usage_limit' => 'sometimes|nullable|integer|min:1',
            'max_usage_per_user' => 'sometimes|nullable|integer|min:1',
            'min_order_amount' => 'sometimes|nullable|numeric|min:0',
            'max_discount' => 'sometimes|nullable|numeric|min:0',
        ]);

        $coupon = Coupon::findOrFail($id);

        $coupon->update($validated);
        return $this->successResponse(
            message: "coupon updated successfully",
            data: ['coupon' => $coupon]
        );
    }


    public function destroy(string $id)
    {

        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return $this->successResponse(message: "coupon deleted successfully");
    }


    public function validate(Request $request)
    {
        $request->validate(['code' => 'required|string|exists:coupons,code']);
        try {
            $coupon = $this->orderService->validateCoupon($request->code, $request->user()->id, $request->subtotal ?? 0);
            return $this->successResponse(message: 'Coupon is valid! ✓');
        } catch (\Exception $e) {
            return $this->errorResponse(message: $e->getMessage());
        }
    }
}
