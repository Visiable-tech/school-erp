<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LateFeeFineRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'name',
        'code',
        'is_default',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function academicYear()
    {
        return $this->belongsTo(
            AcademicYear::class
        );
    }

    public function slabs()
    {
        return $this->hasMany(
            LateFeeFineSlab::class,
            'late_fee_fine_rule_id'
        )->orderBy('sort_order');
    }
}