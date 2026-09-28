<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmGalleryCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
