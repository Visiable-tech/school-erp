<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiscFeeComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'fixed_amount',
        'default_amount',
        'is_refundable',
        'allow_concession',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'fixed_amount' => 'boolean',
        'default_amount' => 'decimal:2',
        'is_refundable' => 'boolean',
        'allow_concession' => 'boolean',
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