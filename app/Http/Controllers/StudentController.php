<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = Student::with([
                'currentEnrollment.academicYear',
                'currentEnrollment.schoolClass',
                'currentEnrollment.section',
            ])
            ->where(
                'school_id',
                $schoolId
            );


        if ($request->filled('search')) {

            $search =
                trim($request->search);

            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'admission_no',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'student_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'father_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'father_mobile',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }


        $students = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'students.index',
            compact('students')
        );
    }


    public function show(
        Student $student
    ) {
        $this->checkAccess($student);


        $student->load([

            'application',

            'currentEnrollment.academicYear',

            'currentEnrollment.schoolClass.wing',

            'currentEnrollment.section',

            'enrollments' => function ($query) {

                $query
                    ->with([
                        'academicYear',
                        'schoolClass',
                        'section',
                    ])
                    ->orderByDesc('id');
            },
        ]);


        return view(
            'students.show',
            compact('student')
        );
    }


    private function checkAccess(
        Student $student
    ): void {

        if (
            $student->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }

    public function edit(Student $student)
    {
        $this->checkAccess($student);

        $student->load([
            'currentEnrollment.academicYear',
            'currentEnrollment.schoolClass',
            'currentEnrollment.section',
        ]);

        return view(
            'students.edit',
            compact('student')
        );
    }


    public function update(
        Request $request,
        Student $student
    ) {
        $this->checkAccess($student);

        $validated = $request->validate([

            'student_name' => [
                'required',
                'string',
                'max:150',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
            ],

            'gender' => [
                'nullable',
                Rule::in([
                    'male',
                    'female',
                    'other',
                ]),
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:10',
            ],

            'nationality' => [
                'nullable',
                'string',
                'max:100',
            ],

            'religion' => [
                'nullable',
                'string',
                'max:100',
            ],

            'category' => [
                'nullable',
                'string',
                'max:50',
            ],

            'mother_tongue' => [
                'nullable',
                'string',
                'max:100',
            ],


            /*
            |--------------------------------------------------------------------------
            | Father
            |--------------------------------------------------------------------------
            */

            'father_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'father_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],

            'father_email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'father_occupation' => [
                'nullable',
                'string',
                'max:150',
            ],


            /*
            |--------------------------------------------------------------------------
            | Mother
            |--------------------------------------------------------------------------
            */

            'mother_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'mother_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],

            'mother_email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'mother_occupation' => [
                'nullable',
                'string',
                'max:150',
            ],


            /*
            |--------------------------------------------------------------------------
            | Guardian
            |--------------------------------------------------------------------------
            */

            'guardian_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'guardian_relation' => [
                'nullable',
                'string',
                'max:100',
            ],

            'guardian_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],


            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'present_address' => [
                'nullable',
                'string',
            ],

            'present_city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'present_state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'present_pin_code' => [
                'nullable',
                'string',
                'max:10',
            ],

            'permanent_address' => [
                'nullable',
                'string',
            ],

            'permanent_city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'permanent_state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'permanent_pin_code' => [
                'nullable',
                'string',
                'max:10',
            ],


            /*
            |--------------------------------------------------------------------------
            | Previous School
            |--------------------------------------------------------------------------
            */

            'previous_school' => [
                'nullable',
                'string',
                'max:200',
            ],

            'previous_class' => [
                'nullable',
                'string',
                'max:100',
            ],

            'previous_board' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],
        ]);


        $validated['status'] =
            $request->boolean('status');


        $student->update($validated);


        return redirect()
            ->route(
                'students.show',
                $student
            )
            ->with(
                'success',
                'Student information updated successfully.'
            );
    }
}