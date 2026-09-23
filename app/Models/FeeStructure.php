<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'school_class_id',
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function items()
    {
        return $this->hasMany(FeeStructureItem::class)
            ->orderBy('sort_order');
    }

    public function feeHeads()
    {
        return $this->belongsToMany(
            FeeHead::class,
            'fee_structure_items'
        )
        ->withPivot([
            'amount',
            'sort_order',
            'status'
        ])
        ->withTimestamps();
    }

    public function installments()
    {
        return $this->hasMany(FeeInstallment::class)
            ->orderBy('sort_order')
            ->orderBy('due_date');
    }
}