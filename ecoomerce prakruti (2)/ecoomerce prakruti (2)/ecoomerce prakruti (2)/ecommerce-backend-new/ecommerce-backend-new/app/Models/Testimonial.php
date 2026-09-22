<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'location',
        'img',
        'rating',
        'msg',
        'status',
        'source',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
