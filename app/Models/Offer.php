<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [

        'title',
        'description',
        'slug',
        'image',
        'status',
        'start_date',
        'expire_date',
        'type',
        'value',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'expire_date' => 'datetime',
        ];
    }

    public function scopeIsValid($query)
    {
        return $query
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('expire_date', '>=', now());
    }



    public function groups() {
        return $this->hasMany(offerGroup::class);
    }


    public function carts(){
        return $this->hasMany(Cart::class);
    }

}
