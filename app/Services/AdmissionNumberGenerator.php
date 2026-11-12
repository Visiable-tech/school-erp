<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdmissionNumberGenerator
{
    /** Call inside DB::transaction(), after locking the school's row. */
    public function generate(int $schoolId, int $academicYearId): string
    {
        $school = School::whereKey($schoolId)->lockForUpdate()->firstOrFail();
        $year = AcademicYear::where('school_id', $schoolId)->findOrFail($academicYearId);

        // school_code is the configurable school abbreviation, e.g. DWPS.
        $code = strtoupper(trim((string) $school->school_code));
        if (!preg_match('/^[A-Z0-9]+$/', $code)) {
            throw ValidationException::withMessages(['school_id' => 'Set a valid alphanumeric school code first.']);
        }
        $startYear = $year->start_date?->format('Y');
        if (!$startYear && preg_match('/(20\d{2})/', (string) $year->name, $matches)) {
            $startYear = $matches[1];
        }
        if (!$startYear) {
            throw ValidationException::withMessages(['academic_year_id' => 'Set the academic session start date first.']);
        }
        $prefix = $code . substr($startYear, -2);

        // Numeric max rather than lexical max: handles sequence 999 -> 1000 correctly.
        $max = Student::where('school_id', $schoolId)
            ->where('admission_no', 'like', $prefix . '%')
            ->whereRaw('admission_no REGEXP ?', ['^' . preg_quote($prefix, '/') . '[0-9]{3,}$'])
            ->selectRaw('MAX(CAST(SUBSTRING(admission_no, ?) AS UNSIGNED)) as max_sequence', [strlen($prefix) + 1])
            ->value('max_sequence');
        return $prefix . str_pad((string) ((int) $max + 1), 3, '0', STR_PAD_LEFT);
    }
}
