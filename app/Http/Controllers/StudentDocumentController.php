<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentEnrollment;

class StudentDocumentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | All Student Documents
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Filter Data
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $sections = collect();

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id')
        ) {
            $sections = Section::where('school_id', $schoolId)
                ->where(
                    'academic_year_id',
                    $request->academic_year_id
                )
                ->where(
                    'school_class_id',
                    $request->school_class_id
                )
                ->where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Documents Query
        |--------------------------------------------------------------------------
        */

        $documents = StudentDocument::with([
                'student.currentEnrollment.academicYear',
                'student.currentEnrollment.schoolClass',
                'student.currentEnrollment.section',
                'uploader',
            ])
            ->where('school_id', $schoolId)


            /*
            |--------------------------------------------------------------------------
            | General Search
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('search'),
                function ($query) use ($request) {

                    $search = trim($request->search);

                    $query->where(
                        function ($q) use ($search) {

                            $q->where(
                                'document_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'document_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'document_type',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhereHas(
                                'student',
                                function ($studentQuery) use ($search) {

                                    $studentQuery
                                        ->where(
                                            'student_name',
                                            'like',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'admission_no',
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
                    );
                }
            )


            /*
            |--------------------------------------------------------------------------
            | Document Type
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('document_type'),
                function ($query) use ($request) {

                    $query->where(
                        'document_type',
                        $request->document_type
                    );
                }
            )


            /*
            |--------------------------------------------------------------------------
            | Document Status
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('status'),
                function ($query) use ($request) {

                    $query->where(
                        'status',
                        $request->status
                    );
                }
            )


            /*
            |--------------------------------------------------------------------------
            | Academic Year
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('academic_year_id'),
                function ($query) use ($request) {

                    $query->whereHas(
                        'student.currentEnrollment',
                        function ($enrollmentQuery) use ($request) {

                            $enrollmentQuery->where(
                                'academic_year_id',
                                $request->academic_year_id
                            );
                        }
                    );
                }
            )


            /*
            |--------------------------------------------------------------------------
            | Class
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('school_class_id'),
                function ($query) use ($request) {

                    $query->whereHas(
                        'student.currentEnrollment',
                        function ($enrollmentQuery) use ($request) {

                            $enrollmentQuery->where(
                                'school_class_id',
                                $request->school_class_id
                            );
                        }
                    );
                }
            )


            /*
            |--------------------------------------------------------------------------
            | Section
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('section_id'),
                function ($query) use ($request) {

                    $query->whereHas(
                        'student.currentEnrollment',
                        function ($enrollmentQuery) use ($request) {

                            $enrollmentQuery->where(
                                'section_id',
                                $request->section_id
                            );
                        }
                    );
                }
            )


            ->latest('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'student-documents.index',
            compact(
                'documents',
                'academicYears',
                'classes',
                'sections'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student Documents Page
    |--------------------------------------------------------------------------
    */

    public function studentDocuments(Student $student)
    {
        $this->checkStudentAccess($student);

        $student->load([
            'documents' => function ($query) {
                $query->with('uploader')
                    ->latest('id');
            },
            'currentEnrollment.academicYear',
            'currentEnrollment.schoolClass',
            'currentEnrollment.section',
        ]);


        return view(
            'student-documents.student',
            compact('student')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Student $student
    ) {
        $this->checkStudentAccess($student);

        $schoolId = auth()->user()->school_id;


        $validated = $request->validate([

            'document_type' => [
                'required',
                Rule::in([
                    'birth_certificate',
                    'aadhaar_card',
                    'transfer_certificate',
                    'previous_marksheet',
                    'address_proof',
                    'medical_certificate',
                    'student_photo',
                    'caste_certificate',
                    'income_certificate',
                    'migration_certificate',
                    'other',
                ]),
            ],

            'document_name' => [
                'required',
                'string',
                'max:200',
            ],

            'document_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);


        $path = $request->file('file')->store(
            'student-documents/'
            . $schoolId
            . '/'
            . $student->id,
            'public'
        );


        StudentDocument::create([

            'school_id' =>
                $schoolId,

            'student_id' =>
                $student->id,

            'document_type' =>
                $validated['document_type'],

            'document_name' =>
                $validated['document_name'],

            'document_number' =>
                $validated['document_number'] ?? null,

            'file_path' =>
                $path,

            'remarks' =>
                $validated['remarks'] ?? null,

            'uploaded_by' =>
                auth()->id(),

            'status' =>
                true,
        ]);


        return redirect()
            ->route('student-documents.index')
            ->with(
                'success',
                'Student document uploaded successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        StudentDocument $studentDocument
    ) {
        $this->checkDocumentAccess(
            $studentDocument
        );

        $studentDocument->load('student');


        return view(
            'student-documents.edit',
            compact('studentDocument')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        StudentDocument $studentDocument
    ) {
        $this->checkDocumentAccess(
            $studentDocument
        );


        $validated = $request->validate([

            'document_type' => [
                'required',
                Rule::in([
                    'birth_certificate',
                    'aadhaar_card',
                    'transfer_certificate',
                    'previous_marksheet',
                    'address_proof',
                    'medical_certificate',
                    'student_photo',
                    'caste_certificate',
                    'income_certificate',
                    'migration_certificate',
                    'other',
                ]),
            ],

            'document_name' => [
                'required',
                'string',
                'max:200',
            ],

            'document_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

        ]);


        $filePath =
            $studentDocument->file_path;


        /*
        |--------------------------------------------------------------------------
        | Replace file if uploaded
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('file')) {

            $newPath = $request->file('file')->store(
                'student-documents/'
                . $studentDocument->school_id
                . '/'
                . $studentDocument->student_id,
                'public'
            );


            if (
                $studentDocument->file_path
                &&
                Storage::disk('public')->exists(
                    $studentDocument->file_path
                )
            ) {

                Storage::disk('public')->delete(
                    $studentDocument->file_path
                );
            }


            $filePath = $newPath;
        }


        $studentDocument->update([

            'document_type' =>
                $validated['document_type'],

            'document_name' =>
                $validated['document_name'],

            'document_number' =>
                $validated['document_number'] ?? null,

            'file_path' =>
                $filePath,

            'remarks' =>
                $validated['remarks'] ?? null,

            'status' =>
                $request->boolean('status'),
        ]);


        return redirect()
            ->route(
                'student-documents.student',
                $studentDocument->student_id
            )
            ->with(
                'success',
                'Student document updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(
        StudentDocument $studentDocument
    ) {
        $this->checkDocumentAccess(
            $studentDocument
        );


        if (
            $studentDocument->file_path
            &&
            Storage::disk('public')->exists(
                $studentDocument->file_path
            )
        ) {

            Storage::disk('public')->delete(
                $studentDocument->file_path
            );
        }


        $studentDocument->delete();


        return back()->with(
            'success',
            'Student document deleted successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Access Checks
    |--------------------------------------------------------------------------
    */

    private function checkStudentAccess(
        Student $student
    ): void {
        if (
            (int) $student->school_id
            !== (int) auth()->user()->school_id
        ) {
            abort(403);
        }
    }


    private function checkDocumentAccess(
        StudentDocument $studentDocument
    ): void {
        if (
            (int) $studentDocument->school_id
            !== (int) auth()->user()->school_id
        ) {
            abort(403);
        }
    }

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

        return view(
            'student-documents.create',
            compact(
                'academicYears',
                'classes',
                'currentAcademicYear'
            )
        );
    }

    public function getSections(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id'  => 'required|integer',
        ]);

        $academicYearExists = AcademicYear::where('school_id', $schoolId)
            ->where('id', $request->academic_year_id)
            ->where('status', 1)
            ->exists();

        if (!$academicYearExists) {
            return response()->json([], 422);
        }

        $classExists = SchoolClass::where('school_id', $schoolId)
            ->where('id', $request->school_class_id)
            ->where('status', 1)
            ->exists();

        if (!$classExists) {
            return response()->json([], 422);
        }

        $sections = Section::where('school_id', $schoolId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name'
            ]);

        return response()->json($sections);
    }

    public function getStudents(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id'  => 'required|integer',
            'section_id'       => 'required|integer',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Verify Section
        |--------------------------------------------------------------------------
        */

        $sectionExists = Section::where('school_id', $schoolId)
            ->where('id', $request->section_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->where('status', 1)
            ->exists();

        if (!$sectionExists) {
            return response()->json([], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Fetch Students From Enrollment
        |--------------------------------------------------------------------------
        */

        $students = StudentEnrollment::query()

            ->join(
                'students',
                'students.id',
                '=',
                'student_enrollments.student_id'
            )

            ->where(
                'student_enrollments.school_id',
                $schoolId
            )

            ->where(
                'student_enrollments.academic_year_id',
                $request->academic_year_id
            )

            ->where(
                'student_enrollments.school_class_id',
                $request->school_class_id
            )

            ->where(
                'student_enrollments.section_id',
                $request->section_id
            )

            ->where(
                'student_enrollments.status',
                1
            )

            ->where(
                'students.status',
                1
            )

            ->orderByRaw(
                'CASE WHEN student_enrollments.roll_no IS NULL THEN 1 ELSE 0 END'
            )

            ->orderBy(
                'student_enrollments.roll_no'
            )

            ->orderBy(
                'students.student_name'
            )

            ->get([
                'students.id',
                'students.student_name',
                'students.admission_no',
                'student_enrollments.roll_no',
            ]);


        return response()->json($students);
    }

}