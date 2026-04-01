<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'status',
        'start_date',
        'expire_date',
        'usage_limit',
        'max_usage_per_user',
        'min_order_amount',
        'max_discount',
        'usage_count'
    ];


    protected $casts = [
        'start_date' => 'datetime',
        'expire_date' => 'datetime',
    ];


    public function orders() {
        return $this->hasMany(Order::class);

    }

    /**
     * check if the coupon is active and within allowed date
     */


    public function isValid()
    {
        return $this->status === 'active'
        && now()->between($this->start_date, $this->expire_date);
    }

    /**
     * check if the user exceeded the max number of usage
     */

    public function checkUserUsageLimit($userId) {
        if ($this->max_usage_per_user ===  null) {
            return true;
        }
        $usagePerUser = $this->orders()->where('user_id', $userId)->count();
        return $usagePerUser < $this->max_usage_per_user;
    }

    /**
     * check if the coupon reached the usage limit
     */

    public function isUnderUsageLimit() {
        return  $this->usage_limit > $this->usage_count;
    }


    /**
     *
     */

    public function minOrderAmount($amount) {
        if ($this->min_order_amount === null) {
            return true;
        }
        return $amount >=  $this->min_order_amount;
    }











}
