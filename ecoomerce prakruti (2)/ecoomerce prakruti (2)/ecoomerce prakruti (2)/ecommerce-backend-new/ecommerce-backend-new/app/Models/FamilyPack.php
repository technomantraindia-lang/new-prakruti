<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyPack extends Model
{
    protected $fillable = [
        'user_id',
        'profile',
        'nutrient_summary',
        'recommendations',
        'monthly_total',
        'next_purchase_at',
        'next_reminder_at',
        'reminder_sent_at',
        'status',
    ];

    protected $casts = [
        'profile' => 'array',
        'nutrient_summary' => 'array',
        'recommendations' => 'array',
        'monthly_total' => 'decimal:2',
        'next_purchase_at' => 'datetime',
        'next_reminder_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
