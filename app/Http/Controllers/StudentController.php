<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\StudentImage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Filter Dropdown Data
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderByDesc('id')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        /*
        * Sections are filtered according to selected
        * Academic Year / Class when those filters exist.
        */
        $sections = Section::where('school_id', $schoolId)
            ->when(
                $request->filled('academic_year_id'),
                fn ($q) => $q->where(
                    'academic_year_id',
                    $request->academic_year_id
                )
            )
            ->when(
                $request->filled('school_class_id'),
                fn ($q) => $q->where(
                    'school_class_id',
                    $request->school_class_id
                )
            )
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Student Query
        |--------------------------------------------------------------------------
        */

        $query = Student::with([
                'currentEnrollment.academicYear',
                'currentEnrollment.schoolClass',
                'currentEnrollment.section',
            ])
            ->where('school_id', $schoolId);


        /*
        |--------------------------------------------------------------------------
        | Student Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Gender
        |--------------------------------------------------------------------------
        */

        if ($request->filled('gender')) {

            $query->where(
                'gender',
                $request->gender
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Academic Year
        |--------------------------------------------------------------------------
        */

        if ($request->filled('academic_year_id')) {

            $academicYearId =
                $request->academic_year_id;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($academicYearId) {

                    $q->where(
                        'academic_year_id',
                        $academicYearId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Class
        |--------------------------------------------------------------------------
        */

        if ($request->filled('school_class_id')) {

            $classId =
                $request->school_class_id;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($classId) {

                    $q->where(
                        'school_class_id',
                        $classId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Section
        |--------------------------------------------------------------------------
        */

        if ($request->filled('section_id')) {

            $sectionId =
                $request->section_id;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($sectionId) {

                    $q->where(
                        'section_id',
                        $sectionId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Enrollment Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('enrollment_status')) {

            $enrollmentStatus =
                $request->enrollment_status;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($enrollmentStatus) {

                    $q->where(
                        'enrollment_status',
                        $enrollmentStatus
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | General Search
        |--------------------------------------------------------------------------
        |
        | Admission No
        | Student Name
        | Father Name
        | Father Mobile
        | Mother Name
        | Mother Mobile
        | Guardian Name
        | Guardian Mobile
        | Roll No
        |
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

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
                    )

                    ->orWhere(
                        'mother_name',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'mother_mobile',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'guardian_name',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhere(
                        'guardian_mobile',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhereHas(
                        'currentEnrollment',
                        function ($enrollmentQuery) use ($search) {

                            $enrollmentQuery->where(
                                'roll_no',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Result
        |--------------------------------------------------------------------------
        */

        $students = $query
            ->orderBy('student_name')
            ->paginate(20)
            ->withQueryString();


        return view(
            'students.index',
            compact(
                'students',
                'academicYears',
                'classes',
                'sections'
            )
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


        $extraDetails = $this->admissionExtras($student);
        $photoPaths = $this->admissionPhotoPaths($student);

        // Student 360: all queries are scoped to the selected student and school.
        $month = request()->input('month', now()->format('Y-m'));
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $month = now()->format('Y-m');
        }
        $attendance = \App\Models\StudentAttendance::query()
            ->where('school_id', $student->school_id)->where('student_id', $student->id)
            ->whereHas('attendanceSession', fn ($q) => $q->whereBetween('attendance_date', [$month.'-01', \Carbon\Carbon::parse($month.'-01')->endOfMonth()->toDateString()]))
            ->with('attendanceSession')->get()->sortBy(fn ($r) => $r->attendanceSession?->attendance_date);
        $feeDues = collect();
        $feePayments = collect();
        if (auth()->user()->canAny(['fee-collection.view', 'fee-structure.view', 'fee-reports.view', 'student-fee.view'])) {
            $feeDues = \App\Models\StudentFeeDue::where('school_id', $student->school_id)->where('student_id', $student->id)->orderByDesc('due_date')->get();
            $feePayments = \App\Models\FeeCollection::where('school_id', $student->school_id)->where('student_id', $student->id)->orderByDesc('payment_date')->get();
        }
        $documents = collect();
        if (auth()->user()->canAny(['student-document.view', 'student-document.index', 'student.view'])) {
            $documents = \App\Models\StudentDocument::where('school_id', $student->school_id)->where('student_id', $student->id)->orderByDesc('id')->get();
        }
        return view('students.show', compact('student', 'extraDetails', 'photoPaths', 'attendance', 'feeDues', 'feePayments', 'documents', 'month'));
    }



    private function admissionExtras(Student $student): ?object
    {
        return DB::table('student_admission_extra_details')
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)->first();
    }

    private function admissionPhotoPaths(Student $student): array
    {
        $paths = StudentImage::where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->whereIn('image_type', ['student', 'father', 'mother', 'guardian', 'parents'])
            ->orderByDesc('id')->get()->unique('image_type')
            ->pluck('image_path', 'image_type')->all();
        if (empty($paths['student']) && $student->student_photo) $paths['student'] = $student->student_photo;
        return $paths;
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

        $extraDetails = $this->admissionExtras($student);
        $photoPaths = $this->admissionPhotoPaths($student);

        return view(
            'students.edit',
            compact('student', 'extraDetails', 'photoPaths')
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

            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_mobile' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:255'],
            'student_aadhaar_last4' => ['nullable', 'digits:4'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'identification_mark' => ['nullable', 'string', 'max:255'],
            'medical_allergies' => ['nullable', 'string', 'max:255'],
            'medical_conditions' => ['nullable', 'string', 'max:255'],
            'transport_required' => ['nullable', Rule::in(['yes','no'])],
            'pickup_person_name' => ['nullable', 'string', 'max:255'],
            'pickup_person_mobile' => ['nullable', 'string', 'max:255'],
            'father_aadhaar_last4' => ['nullable', 'digits:4'],
            'father_nationality' => ['nullable', 'string', 'max:255'],
            'father_qualification' => ['nullable', 'string', 'max:255'],
            'father_organization' => ['nullable', 'string', 'max:255'],
            'father_work_address' => ['nullable', 'string', 'max:255'],
            'father_address' => ['nullable', 'string', 'max:255'],
            'father_city' => ['nullable', 'string', 'max:255'],
            'father_state' => ['nullable', 'string', 'max:255'],
            'father_pin_code' => ['nullable', 'regex:/^[0-9]{6}$/'],
            'father_alternate_mobile' => ['nullable', 'regex:/^[0-9+() -]{7,20}$/'],
            'mother_aadhaar_last4' => ['nullable', 'digits:4'],
            'mother_nationality' => ['nullable', 'string', 'max:255'],
            'mother_qualification' => ['nullable', 'string', 'max:255'],
            'mother_organization' => ['nullable', 'string', 'max:255'],
            'mother_work_address' => ['nullable', 'string', 'max:255'],
            'mother_address' => ['nullable', 'string', 'max:255'],
            'mother_city' => ['nullable', 'string', 'max:255'],
            'mother_state' => ['nullable', 'string', 'max:255'],
            'mother_pin_code' => ['nullable', 'regex:/^[0-9]{6}$/'],
            'mother_alternate_mobile' => ['nullable', 'regex:/^[0-9+() -]{7,20}$/'],
            'guardian_aadhaar_last4' => ['nullable', 'digits:4'],
            'guardian_nationality' => ['nullable', 'string', 'max:255'],
            'guardian_occupation' => ['nullable', 'string', 'max:255'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'guardian_qualification' => ['nullable', 'string', 'max:255'],
            'guardian_address' => ['nullable', 'string', 'max:255'],
            'guardian_city' => ['nullable', 'string', 'max:255'],
            'guardian_state' => ['nullable', 'string', 'max:255'],
            'guardian_pin_code' => ['nullable', 'regex:/^[0-9]{6}$/'],
            'guardian_alternate_mobile' => ['nullable', 'regex:/^[0-9+() -]{7,20}$/'],
            'photos' => ['nullable', 'array'],
            'photos.student' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'photos.father' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'photos.mother' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'photos.guardian' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'photos.parents' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'status' => [
                'nullable',
                'boolean',
            ],
        ]);


        $validated['status'] =
            $request->boolean('status');


        $studentFields = array_intersect_key($validated, array_flip(['student_name', 'date_of_birth', 'gender', 'blood_group', 'nationality', 'religion', 'category', 'mother_tongue', 'father_name', 'father_mobile', 'father_email', 'father_occupation', 'mother_name', 'mother_mobile', 'mother_email', 'mother_occupation', 'guardian_name', 'guardian_relation', 'guardian_mobile', 'present_address', 'present_city', 'present_state', 'present_pin_code', 'permanent_address', 'permanent_city', 'permanent_state', 'permanent_pin_code', 'previous_school', 'previous_class', 'previous_board', 'status']));
        $student->update($studentFields);

        $extraFields = ['emergency_contact_name', 'emergency_contact_mobile', 'emergency_contact_relation', 'student_aadhaar_last4', 'birth_place', 'identification_mark', 'medical_allergies', 'medical_conditions', 'transport_required', 'pickup_person_name', 'pickup_person_mobile', 'father_aadhaar_last4', 'father_nationality', 'father_qualification', 'father_organization', 'father_work_address', 'father_address', 'father_city', 'father_state', 'father_pin_code', 'father_alternate_mobile', 'mother_aadhaar_last4', 'mother_nationality', 'mother_qualification', 'mother_organization', 'mother_work_address', 'mother_address', 'mother_city', 'mother_state', 'mother_pin_code', 'mother_alternate_mobile', 'guardian_aadhaar_last4', 'guardian_nationality', 'guardian_occupation', 'guardian_email', 'guardian_qualification', 'guardian_address', 'guardian_city', 'guardian_state', 'guardian_pin_code', 'guardian_alternate_mobile'];
        $extraValues = array_intersect_key($validated, array_flip($extraFields));
        $extraValues['updated_at'] = now();
        DB::table('student_admission_extra_details')->updateOrInsert(
            ['school_id' => $student->school_id, 'student_id' => $student->id],
            $extraValues
        );

        foreach (['student','father','mother','guardian','parents'] as $type) {
            if (!$request->hasFile("photos.$type")) continue;
            $newPath = $request->file("photos.$type")->store('students/images/'.$student->id, 'public');
            $existing = StudentImage::where('school_id', $student->school_id)
                ->where('student_id', $student->id)->where('image_type', $type)->first();
            StudentImage::updateOrCreate(
                ['school_id' => $student->school_id, 'student_id' => $student->id, 'image_type' => $type],
                ['image_path' => $newPath, 'created_by' => auth()->id()]
            );
            if ($type === 'student') $student->update(['student_photo' => $newPath]);
            if ($existing && $existing->image_path && $existing->image_path !== $newPath) {
                Storage::disk('public')->delete($existing->image_path);
            }
        }


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

    public function editableList(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Dropdown Data
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderByDesc('id')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        $sections = Section::where('school_id', $schoolId)
            ->when(
                $request->filled('academic_year_id'),
                fn ($q) => $q->where(
                    'academic_year_id',
                    $request->academic_year_id
                )
            )
            ->when(
                $request->filled('school_class_id'),
                fn ($q) => $q->where(
                    'school_class_id',
                    $request->school_class_id
                )
            )
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $query = Student::with([
                'currentEnrollment.academicYear',
                'currentEnrollment.schoolClass',
                'currentEnrollment.section',
            ])
            ->where('school_id', $schoolId);


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Gender
        |--------------------------------------------------------------------------
        */

        if ($request->filled('gender')) {

            $query->where(
                'gender',
                $request->gender
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Academic Year
        |--------------------------------------------------------------------------
        */

        if ($request->filled('academic_year_id')) {

            $academicYearId =
                $request->academic_year_id;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($academicYearId) {

                    $q->where(
                        'academic_year_id',
                        $academicYearId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Class
        |--------------------------------------------------------------------------
        */

        if ($request->filled('school_class_id')) {

            $classId =
                $request->school_class_id;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($classId) {

                    $q->where(
                        'school_class_id',
                        $classId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Section
        |--------------------------------------------------------------------------
        */

        if ($request->filled('section_id')) {

            $sectionId =
                $request->section_id;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($sectionId) {

                    $q->where(
                        'section_id',
                        $sectionId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

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
                    )

                    ->orWhere(
                        'mother_mobile',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhereHas(
                        'currentEnrollment',
                        function ($enrollmentQuery) use ($search) {

                            $enrollmentQuery->where(
                                'roll_no',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            );
        }


        $students = $query
            ->orderBy('student_name')
            ->paginate(20)
            ->withQueryString();


        return view(
            'students.editable-list',
            compact(
                'students',
                'academicYears',
                'classes',
                'sections'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Student From Editable List
    |--------------------------------------------------------------------------
    */

    public function updateEditableList(
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

            'father_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],

            'roll_no' => [
                'nullable',
                'string',
                'max:50',
            ],

            'status' => [
                'required',
                'boolean',
            ],

        ]);


        DB::transaction(
            function () use (
                $student,
                $validated
            ) {

                /*
                |--------------------------------------------------------------------------
                | Student
                |--------------------------------------------------------------------------
                */

                $student->update([

                    'student_name' =>
                        $validated['student_name'],

                    'date_of_birth' =>
                        $validated['date_of_birth']
                        ?? null,

                    'gender' =>
                        $validated['gender']
                        ?? null,

                    'father_mobile' =>
                        $validated['father_mobile']
                        ?? null,

                    'status' =>
                        $validated['status'],

                ]);


                /*
                |--------------------------------------------------------------------------
                | Current Enrollment Roll No
                |--------------------------------------------------------------------------
                */

                $enrollment =
                    $student->currentEnrollment;

                if ($enrollment) {

                    $enrollment->update([

                        'roll_no' =>
                            $validated['roll_no']
                            ?? null,

                    ]);
                }

            }
        );


        return redirect()
            ->back()
            ->with(
                'success',
                'Student updated successfully.'
            );
    }

    public function studentImages(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Filter Data
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('id')
            ->get();


        $classes = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->orderBy('name')
            ->get();


        $sections = Section::where(
                'school_id',
                $schoolId
            )
            ->when(
                $request->filled('academic_year_id'),
                fn ($q) => $q->where(
                    'academic_year_id',
                    $request->academic_year_id
                )
            )
            ->when(
                $request->filled('school_class_id'),
                fn ($q) => $q->where(
                    'school_class_id',
                    $request->school_class_id
                )
            )
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $query = Student::with([
                'images',
                'currentEnrollment.academicYear',
                'currentEnrollment.schoolClass',
                'currentEnrollment.section',
            ])
            ->where(
                'school_id',
                $schoolId
            );


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Academic Year
        |--------------------------------------------------------------------------
        */

        if ($request->filled('academic_year_id')) {

            $academicYearId =
                $request->academic_year_id;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($academicYearId) {

                    $q->where(
                        'academic_year_id',
                        $academicYearId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Class
        |--------------------------------------------------------------------------
        */

        if ($request->filled('school_class_id')) {

            $classId =
                $request->school_class_id;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($classId) {

                    $q->where(
                        'school_class_id',
                        $classId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Section
        |--------------------------------------------------------------------------
        */

        if ($request->filled('section_id')) {

            $sectionId =
                $request->section_id;

            $query->whereHas(
                'currentEnrollment',
                function ($q) use ($sectionId) {

                    $q->where(
                        'section_id',
                        $sectionId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

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
                    )

                    ->orWhereHas(
                        'currentEnrollment',
                        function ($enrollment) use ($search) {

                            $enrollment->where(
                                'roll_no',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            );
        }


        $students = $query
            ->orderBy('student_name')
            ->paginate(20)
            ->withQueryString();


        return view(
            'students.update-images',
            compact(
                'students',
                'academicYears',
                'classes',
                'sections'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Student Image
    |--------------------------------------------------------------------------
    */

    public function updateStudentImage(
        Request $request,
        Student $student
    ) {
        $this->checkAccess($student);

        $request->validate([
            'student_photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Delete Old Image
        |--------------------------------------------------------------------------
        */

        if (
            $student->student_photo &&
            Storage::disk('public')->exists(
                $student->student_photo
            )
        ) {

            Storage::disk('public')->delete(
                $student->student_photo
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Upload New Image
        |--------------------------------------------------------------------------
        */

        $path = $request
            ->file('student_photo')
            ->store(
                'students/photos',
                'public'
            );

        /*
        |--------------------------------------------------------------------------
        | Update Student
        |--------------------------------------------------------------------------
        */

        $student->update([
            'student_photo' => $path,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Student image updated successfully.'
            );
    }

    public function uploadStudentImages(
        Request $request,
        Student $student
    ) {
        $this->checkAccess($student);

        $allowedTypes = StudentImage::types();

        $rules = [];

        foreach ($allowedTypes as $type) {

            $rules["images.$type"] = [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ];
        }

        $request->validate($rules);

        /*
        |--------------------------------------------------------------------------
        | Make Sure At Least One Image Was Selected
        |--------------------------------------------------------------------------
        */

        $hasImage = false;

        foreach ($allowedTypes as $type) {

            if ($request->hasFile("images.$type")) {
                $hasImage = true;
                break;
            }
        }

        if (!$hasImage) {

            return redirect()
                ->back()
                ->withErrors([
                    'images' => 'Please select at least one image.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Images
        |--------------------------------------------------------------------------
        */

        foreach ($allowedTypes as $type) {

            if (!$request->hasFile("images.$type")) {
                continue;
            }

            $file = $request->file("images.$type");

            /*
            |--------------------------------------------------------------------------
            | Find Existing Image
            |--------------------------------------------------------------------------
            */

            $existing = StudentImage::where(
                    'school_id',
                    auth()->user()->school_id
                )
                ->where(
                    'student_id',
                    $student->id
                )
                ->where(
                    'image_type',
                    $type
                )
                ->first();


            /*
            |--------------------------------------------------------------------------
            | Store New Image
            |--------------------------------------------------------------------------
            */

            $path = $file->store(
                'students/images/' . $student->id,
                'public'
            );


            /*
            |--------------------------------------------------------------------------
            | Delete Previous File
            |--------------------------------------------------------------------------
            */

            if (
                $existing &&
                $existing->image_path &&
                Storage::disk('public')->exists(
                    $existing->image_path
                )
            ) {

                Storage::disk('public')->delete(
                    $existing->image_path
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Save DB Record
            |--------------------------------------------------------------------------
            */

            StudentImage::updateOrCreate(

                [
                    'student_id' => $student->id,
                    'image_type' => $type,
                ],

                [
                    'school_id' =>
                        auth()->user()->school_id,

                    'image_path' =>
                        $path,

                    'created_by' =>
                        auth()->id(),
                ]

            );


            /*
            |--------------------------------------------------------------------------
            | Existing Student Photo Compatibility
            |--------------------------------------------------------------------------
            */

            if (
                $type === StudentImage::TYPE_STUDENT
            ) {

                $student->update([
                    'student_photo' => $path,
                ]);
            }
        }


        return redirect()
            ->back()
            ->with(
                'success',
                'Student images saved successfully.'
            );
    }

    public function deleteStudentImage(
        Student $student,
        string $imageType
    ) {
        $this->checkAccess($student);

        abort_unless(
            in_array(
                $imageType,
                StudentImage::types(),
                true
            ),
            404
        );


        $image = StudentImage::where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'student_id',
                $student->id
            )
            ->where(
                'image_type',
                $imageType
            )
            ->firstOrFail();


        if (
            $image->image_path &&
            Storage::disk('public')->exists(
                $image->image_path
            )
        ) {

            Storage::disk('public')->delete(
                $image->image_path
            );
        }


        $image->delete();


        /*
        |--------------------------------------------------------------------------
        | Clear Old Student Photo
        |--------------------------------------------------------------------------
        */

        if (
            $imageType ===
            StudentImage::TYPE_STUDENT
        ) {

            $student->update([
                'student_photo' => null,
            ]);
        }


        return redirect()
            ->back()
            ->with(
                'success',
                'Image deleted successfully.'
            );
    }
}