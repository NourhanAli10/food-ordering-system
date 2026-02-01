<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'slug', 
        'status',
        'image',
        'price', 
        'discount_price', 
        'category_id'
    ];


    public function category() {
        return $this->belongsTo(Category::class);
    }
}
