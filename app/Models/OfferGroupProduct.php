<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferGroupProduct extends Model
{
   protected $fillable = [
    'offer_group_id',
    'product_id',
    'extra_price'
   ];


   


   
}
