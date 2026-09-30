<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequeBounceReason extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'bounce_charge',
        'apply_charge',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'bounce_charge' => 'decimal:2',
        'apply_charge' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }
}