<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'product_id',
        'offer_id',
        'user_id',
        'quantity',
        'price'
    ];


    public function product() {
        return $this->belongsto(Product::class);
    }


     public function offer(){
        return $this->belongsTo(Offer::class);
    }


    public function choices() {
        return $this->hasMany(CartOfferChoices::class);
    }


}
