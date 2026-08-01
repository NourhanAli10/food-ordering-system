<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class offerGroup extends Model
{

    protected $table = 'offer_groups';

    protected $fillable = [
        'offer_id',
        'label',
        'max_choices'
    ];



    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'offer_group_products')
        ->withPivot('extra_price');

    }
}
