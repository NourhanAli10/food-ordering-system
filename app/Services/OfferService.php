<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\offerGroup;
use App\Models\OfferGroupProduct;
use App\Models\Product;



class OfferService
{


    public function applyToProduct(Offer $offer, Array $choices)
    {
        $extra_price = 0;
        // dd($offer->groups);

            if ($offer->type === 'combo') { 
                foreach($choices as $choice)
            foreach($choice['products'] as $productId) {
                $offerGroup = OfferGroupProduct::where('offer_group_id', $choice['offer_group_id'])
                ->where('product_id', $productId)->first();             
                $extra_price += $offerGroup->extra_price;
            }
            return $offer->value + $extra_price;
        } elseif($offer->type === 'fixed') {
            return$offer->product->price - $offer->value;
        }
    }
}



