<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Coupon;
use App\Traits\ApiResponsesTrait;
use Exception;

class OrderService
{

    use ApiResponsesTrait;

    public function calculateDelivery(string $area_id) {
        $area = Area::findOrFail($area_id);
        if (! $area){
            throw new \Exception('Invalid area');
        }
        $delivery_fee = $area->delivery_fee;

        return $delivery_fee;
    }
}
