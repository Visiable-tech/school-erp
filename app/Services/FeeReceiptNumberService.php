<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\FeeReceiptScheme;
use RuntimeException;

class FeeReceiptNumberService
{
    public function generate(
        int $schoolId,
        int $academicYearId
    ): string {

        $scheme = FeeReceiptScheme::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->where(function ($query) use ($academicYearId) {

                $query->where(
                    'academic_year_id',
                    $academicYearId
                )
                ->orWhereNull(
                    'academic_year_id'
                );

            })
            ->orderByRaw(
                'academic_year_id IS NULL ASC'
            )
            ->orderByDesc('is_default')
            ->lockForUpdate()
            ->first();

        if (!$scheme) {

            throw new RuntimeException(
                'No active Fee Receipt Number Scheme is configured.'
            );
        }


        $academicYear = AcademicYear::findOrFail(
            $academicYearId
        );


        $lastNumber = (int) (
            $scheme->last_number ?? 0
        );


        if ($lastNumber <= 0) {

            $nextNumber = (int) $scheme->start_number;

        } else {

            $nextNumber = $lastNumber + 1;
        }


        $scheme->last_number = $nextNumber;

        $scheme->save();


        $number = str_pad(
            $nextNumber,
            (int) $scheme->number_length,
            '0',
            STR_PAD_LEFT
        );


        $separator =
            $scheme->separator ?? '';


        $parts = [];


        if (!empty($scheme->prefix)) {
            $parts[] = $scheme->prefix;
        }


        if ($scheme->include_academic_year) {
            $parts[] = $academicYear->name;
        }


        $parts[] = $number;


        if (!empty($scheme->suffix)) {
            $parts[] = $scheme->suffix;
        }


        return implode(
            $separator,
            $parts
        );
    }
}