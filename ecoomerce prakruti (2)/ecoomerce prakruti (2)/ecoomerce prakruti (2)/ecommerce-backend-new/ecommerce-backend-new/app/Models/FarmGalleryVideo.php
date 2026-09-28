<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmGalleryVideo extends Model
{
    protected $fillable = [
        'farm_gallery_item_id',
        'title',
        'video',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(FarmGalleryItem::class, 'farm_gallery_item_id');
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video ? route('media.file', ['path' => $this->video]) : null;
    }
}
