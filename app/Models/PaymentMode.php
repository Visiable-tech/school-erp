<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'mode_type',
        'requires_reference',
        'requires_bank',
        'requires_instrument_date',
        'is_online',
        'is_default',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'requires_reference' => 'boolean',
        'requires_bank' => 'boolean',
        'requires_instrument_date' => 'boolean',
        'is_online' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}