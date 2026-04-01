<?php

namespace App\Services;

use App\Models\Coupon;
use App\Traits\ApiResponsesTrait;


class CouponService
{

    use ApiResponsesTrait;

    public function validateCoupon(string $code, string $userId, $subtotal)
    {
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            throw new \Exception("coupon is invalid");
        }
        if (! $coupon->isValid()) {
            throw new \Exception("coupon is inactive or expired");
        }
        if (! $coupon->checkUserUsageLimit($userId)) {
            throw new \Exception("user usage exceeded");
        }
        if (! $coupon->isUnderUsageLimit()) {
            throw new \Exception("usage limit exceeded");
        }
        if (! $coupon->minOrderAmount($subtotal)) {
            throw new \Exception("minimum order not reached");
        }

        return $coupon;
    }


    public function calculateDiscount( Coupon $coupon, $subtotal)
    {

        if ($coupon->min_order_amount && $coupon->min_order_amount > $subtotal) {
            return 0;
        }

        if ($coupon->type === 'fixed') {
            return  min($coupon->value, $subtotal);
        }

        $discount =  $subtotal * ($coupon->value / 100);
        if ($coupon->max_discount && $discount >  $coupon->max_discount) {
            $discount = $coupon->max_discount;
        }

        return min($discount, $subtotal);
    }

}
