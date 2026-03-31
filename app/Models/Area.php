<?php

namespace App\Models;

use App\Models\City;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'name',
        'delivery_fee',
        'city_id',
    ];




    public function city() {
        return $this->belongsTo(City::class);

    }

    public function orders(){
        return $this->hasMany(Order::class);
    }
}
