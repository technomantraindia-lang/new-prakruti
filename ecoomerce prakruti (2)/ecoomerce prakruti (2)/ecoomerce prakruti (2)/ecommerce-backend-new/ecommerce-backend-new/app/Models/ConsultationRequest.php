<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationRequest extends Model
{
    protected $fillable = [
        'reference_no',
        'name',
        'email',
        'phone',
        'preferred_contact_method',
        'concern',
        'age_range',
        'dietary_preference',
        'preferred_time',
        'consent',
        'status',
        'internal_note',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'consent' => 'boolean',
        ];
    }
}
