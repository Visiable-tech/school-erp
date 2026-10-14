<?php

namespace App\Services;

use App\Models\AdmissionNumberScheme;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdmissionNumberGenerator
{
    /**
     * Must be called inside the transaction that saves the
     * corresponding admission/enquiry/receipt record.
     */
    public function generate(
        int $schoolId,
        string $schemeType,
        ?string $academicYear = null,
        ?string $classCode = null
    ): string {
        if (!DB::transactionLevel()) {
            throw new \LogicException(
                'Number generation must run inside a database transaction.'
            );
        }

        $scheme = AdmissionNumberScheme::query()
            ->where('school_id', $schoolId)
            ->where('scheme_type', $schemeType)
            ->lockForUpdate()
            ->firstOrFail();

        if (!$scheme->status || !$scheme->auto_generate) {
            throw ValidationException::withMessages([
                'number' => 'Automatic numbering is disabled for this scheme.',
            ]);
        }

        if ($scheme->include_academic_year && !$academicYear) {
            throw ValidationException::withMessages([
                'academic_year' => 'Academic year is required.',
            ]);
        }

        if ($scheme->include_class && !$classCode) {
            throw ValidationException::withMessages([
                'school_class' => 'Class is required.',
            ]);
        }

        $next = max(
            (int) $scheme->starting_number,
            (int) $scheme->last_generated_number + 1
        );

        if (strlen((string) $next) > (int) $scheme->number_length) {
            throw ValidationException::withMessages([
                'number' => 'Number sequence has exceeded its configured length.',
            ]);
        }

        $parts = [];

        if ($scheme->fixed_string) {
            $parts[] = $scheme->fixed_string;
        }

        if ($scheme->include_academic_year) {
            $parts[] = $academicYear;
        }

        if ($scheme->include_class) {
            $parts[] = $classCode;
        }

        $parts[] = str_pad(
            (string) $next,
            (int) $scheme->number_length,
            '0',
            STR_PAD_LEFT
        );

        $number = implode($scheme->separator ?? '', $parts);

        $scheme->last_generated_number = $next;
        $scheme->save();

        return $number;
    }
}