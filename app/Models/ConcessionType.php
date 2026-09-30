<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcessionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'concession_mode',
        'default_value',
        'maximum_amount',
        'requires_approval',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'default_value' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'requires_approval' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function studentAssignments()
    {
        return $this->hasMany(
            StudentConcessionAssignment::class
        );
    }
}