<?php

namespace App\Http\Controllers;

use App\Exports\StudentImportTemplateExport;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class StudentImportController extends Controller
{
    public function index()
    {
        return view('students.import');
    }


    /*
    |--------------------------------------------------------------------------
    | Download Template
    |--------------------------------------------------------------------------
    */

    public function downloadTemplate()
    {
        return Excel::download(
            new StudentImportTemplateExport(),
            'student-import-template.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    public function preview(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:10240',
            ],
        ]);


        $schoolId = auth()->user()->school_id;


        $sheets = Excel::toArray(
            [],
            $request->file('file')
        );


        $sheet = $sheets[0] ?? [];


        if (count($sheet) < 2) {
            return back()->with(
                'error',
                'The uploaded file contains no student records.'
            );
        }


        /*
         * First row = heading.
         */
        $headers = array_map(
            fn ($value) =>
                $this->normalizeHeader($value),
            $sheet[0]
        );


        $requiredHeaders = [
            'student_name',
            'admission_date',
            'academic_year',
            'class',
            'section',
        ];


        foreach ($requiredHeaders as $requiredHeader) {

            if (!in_array($requiredHeader, $headers)) {

                return back()->with(
                    'error',
                    'Missing required Excel column: '
                    . $requiredHeader
                );
            }
        }


        $previewRows = [];

        $seenRollNumbers = [];


        foreach (
            array_slice($sheet, 1)
            as $index => $values
        ) {

            $excelRowNumber = $index + 2;


            /*
             * Skip completely empty rows.
             */
            if ($this->rowIsEmpty($values)) {
                continue;
            }


            $row = [];

            foreach ($headers as $key => $header) {

                if ($header === '') {
                    continue;
                }

                $row[$header] =
                    $values[$key] ?? null;
            }


            $result = $this->validateImportRow(
                $row,
                $schoolId,
                $seenRollNumbers
            );


            $previewRows[] = [

                'excel_row' =>
                    $excelRowNumber,

                'data' =>
                    $row,

                'valid' =>
                    empty($result['errors']),

                'errors' =>
                    $result['errors'],

                'resolved' =>
                    $result['resolved'],

            ];
        }


        if (empty($previewRows)) {

            return back()->with(
                'error',
                'No student data was found in the uploaded file.'
            );
        }


        /*
         * Store preview server-side.
         *
         * Do not trust hidden fields containing resolved DB IDs.
         */
        $token = (string) \Illuminate\Support\Str::uuid();


        session()->put(
            'student_import.' . $token,
            [
                'school_id' => $schoolId,
                'user_id' => auth()->id(),
                'rows' => $previewRows,
            ]
        );


        return view(
            'students.import-preview',
            compact(
                'previewRows',
                'token'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Confirm Import
    |--------------------------------------------------------------------------
    */

    public function confirm(Request $request)
    {
        $request->validate([
            'token' => [
                'required',
                'string',
            ],
        ]);


        $sessionKey =
            'student_import.' . $request->token;


        $importData =
            session()->get($sessionKey);


        if (!$importData) {

            return redirect()
                ->route('students.import')
                ->with(
                    'error',
                    'Import preview expired. Please upload the Excel file again.'
                );
        }


        $schoolId =
            auth()->user()->school_id;


        if (
            (int) $importData['school_id']
            !== (int) $schoolId
            ||
            (int) $importData['user_id']
            !== (int) auth()->id()
        ) {

            abort(403);
        }


        $rows = collect(
            $importData['rows']
        )
            ->where('valid', true)
            ->values();


        if ($rows->isEmpty()) {

            return redirect()
                ->route('students.import')
                ->with(
                    'error',
                    'There are no valid student rows to import.'
                );
        }


        /*
         * Revalidate every valid preview row because
         * school data may have changed after preview.
         */
        $freshRows = [];

        $seenRollNumbers = [];


        foreach ($rows as $previewRow) {

            $result =
                $this->validateImportRow(
                    $previewRow['data'],
                    $schoolId,
                    $seenRollNumbers
                );


            if (!empty($result['errors'])) {

                return redirect()
                    ->route('students.import')
                    ->with(
                        'error',
                        'Import data changed or is no longer valid. Please upload and preview the file again.'
                    );
            }


            $freshRows[] = [
                'data' => $previewRow['data'],
                'resolved' => $result['resolved'],
            ];
        }


        $importedCount =
            DB::transaction(
                function () use (
                    $freshRows,
                    $schoolId
                ) {

                    /*
                     * Serializes admission number generation
                     * for this school.
                     */
                    School::whereKey($schoolId)
                        ->lockForUpdate()
                        ->firstOrFail();


                    /*
                     * Lock every destination section used
                     * by this import.
                     */
                    $sectionIds = collect($freshRows)
                        ->pluck('resolved.section_id')
                        ->unique()
                        ->sort()
                        ->values();


                    $sections = Section::whereIn(
                            'id',
                            $sectionIds
                        )
                        ->where(
                            'school_id',
                            $schoolId
                        )
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');


                    /*
                    |--------------------------------------------------------------------------
                    | Capacity Check For Entire Import
                    |--------------------------------------------------------------------------
                    */

                    foreach ($sectionIds as $sectionId) {

                        $section =
                            $sections->get($sectionId);

                        if (!$section) {
                            throw new \RuntimeException(
                                'One of the selected sections no longer exists.'
                            );
                        }


                        if (
                            $section->capacity === null
                            ||
                            $section->capacity <= 0
                        ) {
                            continue;
                        }


                        $newCount = collect($freshRows)
                            ->where(
                                'resolved.section_id',
                                $sectionId
                            )
                            ->count();


                        $existingCount =
                            StudentEnrollment::where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'academic_year_id',
                                collect($freshRows)
                                    ->firstWhere(
                                        'resolved.section_id',
                                        $sectionId
                                    )['resolved']['academic_year_id']
                            )
                            ->where(
                                'school_class_id',
                                collect($freshRows)
                                    ->firstWhere(
                                        'resolved.section_id',
                                        $sectionId
                                    )['resolved']['school_class_id']
                            )
                            ->where(
                                'section_id',
                                $sectionId
                            )
                            ->where(
                                'enrollment_status',
                                'active'
                            )
                            ->where('status', 1)
                            ->count();


                        if (
                            $existingCount + $newCount
                            > $section->capacity
                        ) {

                            throw new \RuntimeException(
                                'Section '
                                . $section->name
                                . ' does not have enough available seats.'
                            );
                        }
                    }


                    $count = 0;


                    foreach ($freshRows as $item) {

                        $row =
                            $item['data'];

                        $resolved =
                            $item['resolved'];


                        /*
                         * Re-check roll number inside
                         * transaction.
                         */
                        $rollNo =
                            $this->cleanValue(
                                $row['roll_no'] ?? null
                            );


                        if ($rollNo !== null) {

                            $exists =
                                StudentEnrollment::where(
                                    'school_id',
                                    $schoolId
                                )
                                ->where(
                                    'academic_year_id',
                                    $resolved['academic_year_id']
                                )
                                ->where(
                                    'school_class_id',
                                    $resolved['school_class_id']
                                )
                                ->where(
                                    'section_id',
                                    $resolved['section_id']
                                )
                                ->where(
                                    'roll_no',
                                    $rollNo
                                )
                                ->exists();


                            if ($exists) {
                                throw new \RuntimeException(
                                    'Duplicate roll number '
                                    . $rollNo
                                    . ' found while importing.'
                                );
                            }
                        }


                        $admissionDate =
                            $this->parseExcelDate(
                                $row['admission_date']
                            );


                        $admissionNo =
                            $this->generateAdmissionNo(
                                $schoolId,
                                $admissionDate
                            );


                        $student =
                            Student::create([

                                'school_id' =>
                                    $schoolId,

                                'admission_application_id' =>
                                    null,

                                'admission_no' =>
                                    $admissionNo,

                                'admission_date' =>
                                    $admissionDate,

                                'student_name' =>
                                    $this->cleanValue(
                                        $row['student_name']
                                    ),

                                'date_of_birth' =>
                                    $this->parseExcelDate(
                                        $row['date_of_birth']
                                            ?? null
                                    ),

                                'gender' =>
                                    $this->cleanValue(
                                        $row['gender'] ?? null
                                    ),

                                'blood_group' =>
                                    $this->cleanValue(
                                        $row['blood_group'] ?? null
                                    ),

                                'nationality' =>
                                    $this->cleanValue(
                                        $row['nationality'] ?? null
                                    ) ?? 'Indian',

                                'religion' =>
                                    $this->cleanValue(
                                        $row['religion'] ?? null
                                    ),

                                'category' =>
                                    $this->cleanValue(
                                        $row['category'] ?? null
                                    ),

                                'mother_tongue' =>
                                    $this->cleanValue(
                                        $row['mother_tongue'] ?? null
                                    ),

                                'father_name' =>
                                    $this->cleanValue(
                                        $row['father_name'] ?? null
                                    ),

                                'father_mobile' =>
                                    $this->cleanValue(
                                        $row['father_mobile'] ?? null
                                    ),

                                'father_email' =>
                                    $this->cleanValue(
                                        $row['father_email'] ?? null
                                    ),

                                'father_occupation' =>
                                    $this->cleanValue(
                                        $row['father_occupation'] ?? null
                                    ),

                                'mother_name' =>
                                    $this->cleanValue(
                                        $row['mother_name'] ?? null
                                    ),

                                'mother_mobile' =>
                                    $this->cleanValue(
                                        $row['mother_mobile'] ?? null
                                    ),

                                'mother_email' =>
                                    $this->cleanValue(
                                        $row['mother_email'] ?? null
                                    ),

                                'mother_occupation' =>
                                    $this->cleanValue(
                                        $row['mother_occupation'] ?? null
                                    ),

                                'guardian_name' =>
                                    $this->cleanValue(
                                        $row['guardian_name'] ?? null
                                    ),

                                'guardian_relation' =>
                                    $this->cleanValue(
                                        $row['guardian_relation'] ?? null
                                    ),

                                'guardian_mobile' =>
                                    $this->cleanValue(
                                        $row['guardian_mobile'] ?? null
                                    ),

                                'present_address' =>
                                    $this->cleanValue(
                                        $row['present_address'] ?? null
                                    ),

                                'present_city' =>
                                    $this->cleanValue(
                                        $row['present_city'] ?? null
                                    ),

                                'present_state' =>
                                    $this->cleanValue(
                                        $row['present_state'] ?? null
                                    ),

                                'present_pin_code' =>
                                    $this->cleanValue(
                                        $row['present_pin_code'] ?? null
                                    ),

                                'permanent_address' =>
                                    $this->cleanValue(
                                        $row['permanent_address'] ?? null
                                    ),

                                'permanent_city' =>
                                    $this->cleanValue(
                                        $row['permanent_city'] ?? null
                                    ),

                                'permanent_state' =>
                                    $this->cleanValue(
                                        $row['permanent_state'] ?? null
                                    ),

                                'permanent_pin_code' =>
                                    $this->cleanValue(
                                        $row['permanent_pin_code'] ?? null
                                    ),

                                'previous_school' =>
                                    $this->cleanValue(
                                        $row['previous_school'] ?? null
                                    ),

                                'previous_class' =>
                                    $this->cleanValue(
                                        $row['previous_class'] ?? null
                                    ),

                                'previous_board' =>
                                    $this->cleanValue(
                                        $row['previous_board'] ?? null
                                    ),

                                'created_by' =>
                                    auth()->id(),

                                'status' =>
                                    true,
                            ]);


                        StudentEnrollment::create([

                            'school_id' =>
                                $schoolId,

                            'student_id' =>
                                $student->id,

                            'academic_year_id' =>
                                $resolved['academic_year_id'],

                            'school_class_id' =>
                                $resolved['school_class_id'],

                            'section_id' =>
                                $resolved['section_id'],

                            'roll_no' =>
                                $rollNo,

                            'enrollment_status' =>
                                'active',

                            'enrollment_date' =>
                                $admissionDate,

                            'is_current' =>
                                true,

                            'status' =>
                                true,
                        ]);


                        $count++;
                    }


                    return $count;
                }
            );


        session()->forget($sessionKey);


        return redirect()
            ->route('students.index')
            ->with(
                'success',
                $importedCount
                . ' students imported successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Import Row
    |--------------------------------------------------------------------------
    */

    private function validateImportRow(
        array $row,
        int $schoolId,
        array &$seenRollNumbers
    ): array {

        $errors = [];

        $resolved = [];


        $studentName =
            $this->cleanValue(
                $row['student_name'] ?? null
            );


        if (!$studentName) {
            $errors[] =
                'Student name is required.';
        }


        $admissionDate =
            $this->parseExcelDate(
                $row['admission_date'] ?? null
            );


        if (!$admissionDate) {
            $errors[] =
                'Valid admission date is required.';
        }


        $academicYearName =
            $this->cleanValue(
                $row['academic_year'] ?? null
            );


        $className =
            $this->cleanValue(
                $row['class'] ?? null
            );


        $sectionName =
            $this->cleanValue(
                $row['section'] ?? null
            );


        /*
        |--------------------------------------------------------------------------
        | Academic Year
        |--------------------------------------------------------------------------
        */

        $academicYear =
            AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where(
                'name',
                $academicYearName
            )
            ->where(
                'status',
                1
            )
            ->first();


        if (!$academicYear) {

            $errors[] =
                'Academic year "'
                . ($academicYearName ?? '')
                . '" not found.';

        } else {

            $resolved['academic_year_id'] =
                $academicYear->id;
        }


        /*
        |--------------------------------------------------------------------------
        | Class
        |--------------------------------------------------------------------------
        */

        $schoolClass =
            SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where(
                'name',
                $className
            )
            ->where(
                'status',
                1
            )
            ->first();


        if (!$schoolClass) {

            $errors[] =
                'Class "'
                . ($className ?? '')
                . '" not found.';

        } else {

            $resolved['school_class_id'] =
                $schoolClass->id;
        }


        /*
        |--------------------------------------------------------------------------
        | Section
        |--------------------------------------------------------------------------
        */

        $section = null;


        if ($academicYear && $schoolClass) {

            $section =
                Section::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'academic_year_id',
                    $academicYear->id
                )
                ->where(
                    'school_class_id',
                    $schoolClass->id
                )
                ->where(
                    'name',
                    $sectionName
                )
                ->where(
                    'status',
                    1
                )
                ->first();


            if (!$section) {

                $errors[] =
                    'Section "'
                    . ($sectionName ?? '')
                    . '" not found for the selected year/class.';

            } else {

                $resolved['section_id'] =
                    $section->id;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Gender
        |--------------------------------------------------------------------------
        */

        $gender =
            $this->cleanValue(
                $row['gender'] ?? null
            );


        if (
            $gender
            &&
            !in_array(
                strtolower($gender),
                ['male', 'female', 'other'],
                true
            )
        ) {

            $errors[] =
                'Gender must be Male, Female or Other.';
        }


        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        foreach (
            ['father_email', 'mother_email']
            as $emailField
        ) {

            $email =
                $this->cleanValue(
                    $row[$emailField] ?? null
                );


            if (
                $email
                &&
                !filter_var(
                    $email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {

                $errors[] =
                    str_replace(
                        '_',
                        ' ',
                        ucfirst($emailField)
                    )
                    . ' is invalid.';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Roll Number
        |--------------------------------------------------------------------------
        */

        $rollNo =
            $this->cleanValue(
                $row['roll_no'] ?? null
            );


        if (
            $rollNo
            &&
            $academicYear
            &&
            $schoolClass
            &&
            $section
        ) {

            $rollKey =
                $academicYear->id
                . '|'
                . $schoolClass->id
                . '|'
                . $section->id
                . '|'
                . strtolower($rollNo);


            if (
                isset(
                    $seenRollNumbers[$rollKey]
                )
            ) {

                $errors[] =
                    'Duplicate roll number in Excel file.';

            } else {

                $seenRollNumbers[$rollKey] =
                    true;
            }


            $exists =
                StudentEnrollment::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'academic_year_id',
                    $academicYear->id
                )
                ->where(
                    'school_class_id',
                    $schoolClass->id
                )
                ->where(
                    'section_id',
                    $section->id
                )
                ->where(
                    'roll_no',
                    $rollNo
                )
                ->exists();


            if ($exists) {

                $errors[] =
                    'Roll number already exists in the selected section.';
            }
        }


        return [
            'errors' => $errors,
            'resolved' => $resolved,
        ];
    }


    private function normalizeHeader(
        $header
    ): string {

        return strtolower(
            trim(
                preg_replace(
                    '/[^A-Za-z0-9]+/',
                    '_',
                    (string) $header
                ),
                '_'
            )
        );
    }


    private function cleanValue(
        $value
    ): ?string {

        if ($value === null) {
            return null;
        }


        $value =
            trim((string) $value);


        return $value === ''
            ? null
            : $value;
    }


    private function rowIsEmpty(
        array $row
    ): bool {

        foreach ($row as $value) {

            if (
                $value !== null
                &&
                trim((string) $value) !== ''
            ) {
                return false;
            }
        }


        return true;
    }


    private function parseExcelDate(
        $value
    ): ?string {

        if (
            $value === null
            ||
            $value === ''
        ) {
            return null;
        }


        try {

            /*
             * Native Excel date number.
             */
            if (is_numeric($value)) {

                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date
                        ::excelToDateTimeObject($value)
                )->format('Y-m-d');
            }


            return Carbon::parse(
                $value
            )->format('Y-m-d');


        } catch (\Throwable $e) {

            return null;
        }
    }


    private function generateAdmissionNo(
        int $schoolId,
        string $date
    ): string {

        $year =
            Carbon::parse($date)
                ->format('Y');


        $prefix =
            'ADM-' . $year . '-';


        $lastNo =
            Student::where(
                'school_id',
                $schoolId
            )
            ->where(
                'admission_no',
                'like',
                $prefix . '%'
            )
            ->orderByDesc(
                'admission_no'
            )
            ->value(
                'admission_no'
            );


        $sequence =
            $lastNo
                ? (int) substr(
                    $lastNo,
                    -5
                )
                : 0;


        return $prefix
            . str_pad(
                $sequence + 1,
                5,
                '0',
                STR_PAD_LEFT
            );
    }
}