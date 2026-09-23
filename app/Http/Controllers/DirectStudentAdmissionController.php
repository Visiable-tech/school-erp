<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DirectStudentAdmissionController extends Controller
{
    public function create()
    {
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $currentAcademicYear = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->where('is_current', 1)
            ->first();

        return view('students.create', compact(
            'academicYears',
            'classes',
            'currentAcademicYear'
        ));
    }


    public function getSections(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([
            'academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')
                    ->where(fn ($q) =>
                        $q->where('school_id', $schoolId)
                          ->where('status', 1)
                    ),
            ],

            'school_class_id' => [
                'required',
                Rule::exists('school_classes', 'id')
                    ->where(fn ($q) =>
                        $q->where('school_id', $schoolId)
                          ->where('status', 1)
                    ),
            ],
        ]);

        $sections = Section::where('school_id', $schoolId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'capacity']);

        return response()->json($sections);
    }


    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([

            'admission_date' => ['required', 'date'],

            'student_name' => [
                'required',
                'string',
                'max:150',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'gender' => [
                'nullable',
                Rule::in(['Male', 'Female', 'Other']),
            ],

            'blood_group' => ['nullable', 'string', 'max:20'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'mother_tongue' => ['nullable', 'string', 'max:100'],

            'father_name' => ['nullable', 'string', 'max:150'],
            'father_mobile' => ['nullable', 'string', 'max:20'],
            'father_email' => ['nullable', 'email', 'max:150'],
            'father_occupation' => ['nullable', 'string', 'max:150'],

            'mother_name' => ['nullable', 'string', 'max:150'],
            'mother_mobile' => ['nullable', 'string', 'max:20'],
            'mother_email' => ['nullable', 'email', 'max:150'],
            'mother_occupation' => ['nullable', 'string', 'max:150'],

            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'guardian_mobile' => ['nullable', 'string', 'max:20'],

            'present_address' => ['nullable', 'string'],
            'present_city' => ['nullable', 'string', 'max:100'],
            'present_state' => ['nullable', 'string', 'max:100'],
            'present_pin_code' => ['nullable', 'string', 'max:10'],

            'permanent_address' => ['nullable', 'string'],
            'permanent_city' => ['nullable', 'string', 'max:100'],
            'permanent_state' => ['nullable', 'string', 'max:100'],
            'permanent_pin_code' => ['nullable', 'string', 'max:10'],

            'previous_school' => ['nullable', 'string', 'max:200'],
            'previous_class' => ['nullable', 'string', 'max:100'],
            'previous_board' => ['nullable', 'string', 'max:100'],

            'academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')
                    ->where(fn ($q) =>
                        $q->where('school_id', $schoolId)
                          ->where('status', 1)
                    ),
            ],

            'school_class_id' => [
                'required',
                Rule::exists('school_classes', 'id')
                    ->where(fn ($q) =>
                        $q->where('school_id', $schoolId)
                          ->where('status', 1)
                    ),
            ],

            'section_id' => ['required', 'integer'],

            'roll_no' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);


        $section = Section::where('school_id', $schoolId)
            ->where('id', $validated['section_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('school_class_id', $validated['school_class_id'])
            ->where('status', 1)
            ->first();

        if (!$section) {
            throw ValidationException::withMessages([
                'section_id' =>
                    'The selected section does not belong to the selected academic year and class.',
            ]);
        }


        $student = DB::transaction(function () use (
            $validated,
            $schoolId,
            $section
        ) {

            School::whereKey($schoolId)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedSection = Section::whereKey($section->id)
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Capacity
            |--------------------------------------------------------------------------
            */

            if (
                $lockedSection->capacity !== null
                && $lockedSection->capacity > 0
            ) {

                $currentCount = StudentEnrollment::where('school_id', $schoolId)
                    ->where('academic_year_id', $validated['academic_year_id'])
                    ->where('school_class_id', $validated['school_class_id'])
                    ->where('section_id', $lockedSection->id)
                    ->where('enrollment_status', 'active')
                    ->where('status', 1)
                    ->count();

                if ($currentCount >= $lockedSection->capacity) {
                    throw ValidationException::withMessages([
                        'section_id' =>
                            'The selected section has reached its maximum capacity.',
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Roll Number
            |--------------------------------------------------------------------------
            */

            if (!empty($validated['roll_no'])) {

                $rollExists = StudentEnrollment::where('school_id', $schoolId)
                    ->where('academic_year_id', $validated['academic_year_id'])
                    ->where('school_class_id', $validated['school_class_id'])
                    ->where('section_id', $lockedSection->id)
                    ->where('roll_no', $validated['roll_no'])
                    ->exists();

                if ($rollExists) {
                    throw ValidationException::withMessages([
                        'roll_no' =>
                            'This roll number already exists in the selected section.',
                    ]);
                }
            }


            $admissionNo = $this->generateAdmissionNo(
                $schoolId,
                $validated['admission_date']
            );


            $student = Student::create([

                'school_id' => $schoolId,

                'admission_application_id' => null,

                'admission_no' => $admissionNo,

                'admission_date' => $validated['admission_date'],

                'student_name' => $validated['student_name'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'blood_group' => $validated['blood_group'] ?? null,
                'nationality' => $validated['nationality'] ?? 'Indian',
                'religion' => $validated['religion'] ?? null,
                'category' => $validated['category'] ?? null,
                'mother_tongue' => $validated['mother_tongue'] ?? null,

                'father_name' => $validated['father_name'] ?? null,
                'father_mobile' => $validated['father_mobile'] ?? null,
                'father_email' => $validated['father_email'] ?? null,
                'father_occupation' => $validated['father_occupation'] ?? null,

                'mother_name' => $validated['mother_name'] ?? null,
                'mother_mobile' => $validated['mother_mobile'] ?? null,
                'mother_email' => $validated['mother_email'] ?? null,
                'mother_occupation' => $validated['mother_occupation'] ?? null,

                'guardian_name' => $validated['guardian_name'] ?? null,
                'guardian_relation' => $validated['guardian_relation'] ?? null,
                'guardian_mobile' => $validated['guardian_mobile'] ?? null,

                'present_address' => $validated['present_address'] ?? null,
                'present_city' => $validated['present_city'] ?? null,
                'present_state' => $validated['present_state'] ?? null,
                'present_pin_code' => $validated['present_pin_code'] ?? null,

                'permanent_address' => $validated['permanent_address'] ?? null,
                'permanent_city' => $validated['permanent_city'] ?? null,
                'permanent_state' => $validated['permanent_state'] ?? null,
                'permanent_pin_code' => $validated['permanent_pin_code'] ?? null,

                'previous_school' => $validated['previous_school'] ?? null,
                'previous_class' => $validated['previous_class'] ?? null,
                'previous_board' => $validated['previous_board'] ?? null,

                'created_by' => auth()->id(),

                'status' => true,
            ]);


            StudentEnrollment::create([

                'school_id' => $schoolId,

                'student_id' => $student->id,

                'academic_year_id' =>
                    $validated['academic_year_id'],

                'school_class_id' =>
                    $validated['school_class_id'],

                'section_id' =>
                    $lockedSection->id,

                'roll_no' =>
                    $validated['roll_no'] ?? null,

                'enrollment_status' =>
                    'active',

                'enrollment_date' =>
                    $validated['admission_date'],

                'is_current' =>
                    true,

                'status' =>
                    true,
            ]);


            return $student;
        });


        return redirect()
            ->route('students.show', $student)
            ->with(
                'success',
                'Student admitted successfully. Admission No: '
                . $student->admission_no
            );
    }


    private function generateAdmissionNo(
        int $schoolId,
        string $admissionDate
    ): string {

        $year = Carbon::parse($admissionDate)->format('Y');

        $prefix = 'ADM-' . $year . '-';


        $lastNo = Student::where('school_id', $schoolId)
            ->where('admission_no', 'like', $prefix . '%')
            ->orderByDesc('admission_no')
            ->value('admission_no');


        $lastSequence = $lastNo
            ? (int) substr($lastNo, -5)
            : 0;


        return $prefix
            . str_pad(
                $lastSequence + 1,
                5,
                '0',
                STR_PAD_LEFT
            );
    }
}