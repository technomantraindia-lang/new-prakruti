<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmGalleryItem extends Model
{
    protected $fillable = [
        'category',
        'title',
        'description',
        'image',
        'tag',
        'location',
        'details',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function videos(): HasMany
    {
        return $this->hasMany(FarmGalleryVideo::class)->orderBy('sort_order');
    }

    public function categoryInfo(): BelongsTo
    {
        return $this->belongsTo(FarmGalleryCategory::class, 'category', 'slug');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? route('media.file', ['path' => $this->image]) : null;
    }

    public function getCategoryNameAttribute(): string
    {
        return $this->categoryInfo?->name ?? str_replace('-', ' ', ucfirst($this->category));
    }
}
