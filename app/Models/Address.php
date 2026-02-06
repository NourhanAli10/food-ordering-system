<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Workbench\App\Models\User;

class Address extends Model
{
    protected $fillable = [
        'user_id', 
        'address', 
        'floor',
        'apartment',
        'city',
        'is_default'
    ];



    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts() {
        return [
            'is_default' => 'boolean'
        ];
    } 


    public function user() {
        return $this->belongsTo(User::class);
    }
}
