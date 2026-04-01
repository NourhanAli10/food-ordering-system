<?php

namespace App\Models;

use App\Models\Area;
use App\Models\Coupon;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'coupon_id',
        'name',
        'phone',
        'address',
        'floor',
        'apartment',
        'building_number',
        'city',
        'type',
        'status',
        'subtotal',
        'discount',
        'tax',
        'delivery_fee',
        'total',
        'notes',
        'delivered_at',
        'area_id'


    ];


    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
    
}
