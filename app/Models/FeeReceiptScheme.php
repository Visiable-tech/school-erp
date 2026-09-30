<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeReceiptScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'name',
        'code',
        'prefix',
        'suffix',
        'separator',
        'include_academic_year',
        'number_length',
        'start_number',
        'last_number',
        'reset_yearly',
        'is_default',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'include_academic_year' => 'boolean',
        'number_length' => 'integer',
        'start_number' => 'integer',
        'last_number' => 'integer',
        'reset_yearly' => 'boolean',
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

    /**
     * Preview only.
     *
     * IMPORTANT:
     * This does NOT update last_number.
     */
    public function previewNextNumber(): string
    {
        $nextNumber = $this->last_number > 0
            ? $this->last_number + 1
            : $this->start_number;

        return $this->formatNumber(
            $nextNumber
        );
    }

    public function formatNumber(
        int $number
    ): string {

        $parts = [];

        if ($this->prefix) {
            $parts[] = $this->prefix;
        }

        if (
            $this->include_academic_year &&
            $this->academicYear
        ) {
            $parts[] =
                $this->academicYear->name;
        }

        $parts[] = str_pad(
            (string) $number,
            $this->number_length,
            '0',
            STR_PAD_LEFT
        );

        if ($this->suffix) {
            $parts[] = $this->suffix;
        }

        return implode(
            $this->separator,
            $parts
        );
    }
}