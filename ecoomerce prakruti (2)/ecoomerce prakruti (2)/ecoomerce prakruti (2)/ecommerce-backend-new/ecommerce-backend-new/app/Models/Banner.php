<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = ['title', 'image', 'link', 'button_text', 'sort_order', 'status'];

    protected $casts = [
        'sort_order' => 'integer',
        'status' => 'string',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image || ! Storage::disk('public')->exists($this->image)) {
            return null;
        }

        return route('media.file', ['path' => $this->image]);
    }
}
