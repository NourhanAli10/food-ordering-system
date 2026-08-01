<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartOfferChoices extends Model
{
    protected $fillable = [
        'cart_id',
        'offer_group_id',
        'product_id',
    ];



    public function cart(){
        return $this->belongsTo(cart::class);
    }


    public function product(){
        return $this->belongsTo(Product::class);

    }
}
