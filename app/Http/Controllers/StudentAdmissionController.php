<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\School;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentAdmissionController extends Controller
{
    public function create(
        AdmissionApplication $admissionApplication
    ) {
        $this->checkAccess(
            $admissionApplication
        );

        /*
         * Only approved applications can
         * become students.
         */
        if (
            $admissionApplication->application_status
            !== 'approved'
        ) {
            return back()->with(
                'error',
                'Only approved applications can be admitted.'
            );
        }


        /*
         * Prevent duplicate student.
         */
        if (
            $admissionApplication
                ->student()
                ->exists()
        ) {
            return redirect()
                ->route(
                    'students.show',
                    $admissionApplication->student
                )
                ->with(
                    'error',
                    'Student has already been created from this application.'
                );
        }


        /*
         * Sections must belong to:
         * same school
         * same academic year
         * same class
         */
        $sections = Section::where(
                'school_id',
                $admissionApplication->school_id
            )
            ->where(
                'academic_year_id',
                $admissionApplication->academic_year_id
            )
            ->where(
                'school_class_id',
                $admissionApplication->school_class_id
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        $admissionApplication->load([
            'schoolClass',
            'academicYear',
            'admissionSession',
        ]);


        return view(
            'student-admissions.create',
            compact(
                'admissionApplication',
                'sections'
            )
        );
    }


    public function store(
        Request $request,
        AdmissionApplication $admissionApplication
    ) {
        $this->checkAccess(
            $admissionApplication
        );


        if (
            $admissionApplication->application_status
            !== 'approved'
        ) {
            return back()->with(
                'error',
                'Only approved applications can be admitted.'
            );
        }


        if (
            $admissionApplication
                ->student()
                ->exists()
        ) {
            return back()->with(
                'error',
                'Student already exists for this application.'
            );
        }


        $schoolId =
            $admissionApplication->school_id;


        $validated = $request->validate([

            'admission_date' => [
                'required',
                'date',
            ],

            'section_id' => [
                'required',

                Rule::exists(
                    'sections',
                    'id'
                )->where(
                    fn ($query) =>
                    $query
                        ->where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'academic_year_id',
                            $admissionApplication
                                ->academic_year_id
                        )
                        ->where(
                            'school_class_id',
                            $admissionApplication
                                ->school_class_id
                        )
                        ->where(
                            'status',
                            1
                        )
                ),
            ],

            'roll_no' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);


        $student = DB::transaction(
            function () use (
                $validated,
                $admissionApplication,
                $schoolId
            ) {

                /*
                |--------------------------------------------------------------------------
                | Lock School
                |--------------------------------------------------------------------------
                |
                | Serializes admission number generation
                | for this school.
                |
                */

                School::whereKey($schoolId)
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | Generate Admission Number
                |--------------------------------------------------------------------------
                */

                $year = Carbon::parse(
                    $validated['admission_date']
                )->format('Y');


                $prefix =
                    'ADM-' .
                    $year .
                    '-';


                $lastAdmissionNo =
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


                $lastSequence =
                    $lastAdmissionNo
                        ? (int) substr(
                            $lastAdmissionNo,
                            -5
                        )
                        : 0;


                $admissionNo =
                    $prefix .
                    str_pad(
                        $lastSequence + 1,
                        5,
                        '0',
                        STR_PAD_LEFT
                    );


                /*
                |--------------------------------------------------------------------------
                | Create Student
                |--------------------------------------------------------------------------
                */

                $student = Student::create([

                    'school_id' =>
                        $schoolId,

                    'admission_application_id' =>
                        $admissionApplication->id,

                    'admission_no' =>
                        $admissionNo,

                    'admission_date' =>
                        $validated['admission_date'],

                    /*
                     * Student
                     */

                    'student_name' =>
                        $admissionApplication
                            ->student_name,

                    'date_of_birth' =>
                        $admissionApplication
                            ->date_of_birth,

                    'gender' =>
                        $admissionApplication
                            ->gender,

                    'blood_group' =>
                        $admissionApplication
                            ->blood_group,

                    'nationality' =>
                        $admissionApplication
                            ->nationality,

                    'religion' =>
                        $admissionApplication
                            ->religion,

                    'category' =>
                        $admissionApplication
                            ->category,

                    'mother_tongue' =>
                        $admissionApplication
                            ->mother_tongue,

                    /*
                     * Father
                     */

                    'father_name' =>
                        $admissionApplication
                            ->father_name,

                    'father_mobile' =>
                        $admissionApplication
                            ->father_mobile,

                    'father_email' =>
                        $admissionApplication
                            ->father_email,

                    'father_occupation' =>
                        $admissionApplication
                            ->father_occupation,

                    /*
                     * Mother
                     */

                    'mother_name' =>
                        $admissionApplication
                            ->mother_name,

                    'mother_mobile' =>
                        $admissionApplication
                            ->mother_mobile,

                    'mother_email' =>
                        $admissionApplication
                            ->mother_email,

                    'mother_occupation' =>
                        $admissionApplication
                            ->mother_occupation,

                    /*
                     * Guardian
                     */

                    'guardian_name' =>
                        $admissionApplication
                            ->guardian_name,

                    'guardian_relation' =>
                        $admissionApplication
                            ->guardian_relation,

                    'guardian_mobile' =>
                        $admissionApplication
                            ->guardian_mobile,

                    /*
                     * Present Address
                     */

                    'present_address' =>
                        $admissionApplication
                            ->present_address,

                    'present_city' =>
                        $admissionApplication
                            ->present_city,

                    'present_state' =>
                        $admissionApplication
                            ->present_state,

                    'present_pin_code' =>
                        $admissionApplication
                            ->present_pin_code,

                    /*
                     * Permanent Address
                     */

                    'permanent_address' =>
                        $admissionApplication
                            ->permanent_address,

                    'permanent_city' =>
                        $admissionApplication
                            ->permanent_city,

                    'permanent_state' =>
                        $admissionApplication
                            ->permanent_state,

                    'permanent_pin_code' =>
                        $admissionApplication
                            ->permanent_pin_code,

                    /*
                     * Previous School
                     */

                    'previous_school' =>
                        $admissionApplication
                            ->previous_school,

                    'previous_class' =>
                        $admissionApplication
                            ->previous_class,

                    'previous_board' =>
                        $admissionApplication
                            ->previous_board,

                    'created_by' =>
                        auth()->id(),

                    'status' =>
                        true,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Create Academic Enrollment
                |--------------------------------------------------------------------------
                */

                StudentEnrollment::create([

                    'school_id' =>
                        $schoolId,

                    'student_id' =>
                        $student->id,

                    'academic_year_id' =>
                        $admissionApplication
                            ->academic_year_id,

                    'school_class_id' =>
                        $admissionApplication
                            ->school_class_id,

                    'section_id' =>
                        $validated['section_id'],

                    'roll_no' =>
                        $validated['roll_no']
                        ?? null,

                    'enrollment_status' =>
                        'active',

                    'enrollment_date' =>
                        $validated['admission_date'],

                    'is_current' =>
                        true,

                    'status' =>
                        true,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Update Application
                |--------------------------------------------------------------------------
                */

                $admissionApplication->update([

                    'application_status' =>
                        'admitted',
                ]);


                /*
                |--------------------------------------------------------------------------
                | Update Enquiry
                |--------------------------------------------------------------------------
                */

                $admissionApplication
                    ->enquiry
                    ->update([

                        'enquiry_status' =>
                            'admitted',

                        'next_followup_date' =>
                            null,
                    ]);


                return $student;
            }
        );


        return redirect()
            ->route(
                'students.show',
                $student
            )
            ->with(
                'success',
                "Student admitted successfully. Admission No: {$student->admission_no}"
            );
    }


    private function checkAccess(
        AdmissionApplication $application
    ): void {

        if (
            $application->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }
}