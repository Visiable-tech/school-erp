<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassSubjectController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::with('wing')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $query = ClassSubject::with([
                'academicYear',
                'schoolClass.wing',
                'subject'
            ])
            ->where('school_id', $schoolId);

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if ($request->filled('school_class_id')) {
            $query->where(
                'school_class_id',
                $request->school_class_id
            );
        }

        $classSubjects = $query
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('school_class_id')
            ->orderBy('sort_order')
            ->paginate(30)
            ->withQueryString();

        return view(
            'class-subjects.index',
            compact(
                'classSubjects',
                'academicYears',
                'classes'
            )
        );
    }


    public function create()
    {
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::with('wing')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'class-subjects.create',
            compact(
                'academicYears',
                'classes',
                'subjects'
            )
        );
    }


    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        if (!$schoolId) {
            return back()
                ->withInput()
                ->withErrors([
                    'school' => 'No school is assigned to this user.'
                ]);
        }

        $validated = $request->validate([

            'academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    ),
            ],

            'school_class_id' => [
                'required',
                Rule::exists('school_classes', 'id')
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    ),
            ],

            'subjects' => [
                'required',
                'array',
                'min:1',
            ],

            'subjects.*' => [
                'integer',
                Rule::exists('subjects', 'id')
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    ),
            ],

        ]);

        DB::transaction(function () use (
            $validated,
            $request,
            $schoolId
        ) {

            $selectedSubjects = collect(
                $validated['subjects']
            )->map(function ($id) {
                return (int) $id;
            });


            /*
            * Remove subjects that user unchecked.
            */
            ClassSubject::where('school_id', $schoolId)
                ->where(
                    'academic_year_id',
                    $validated['academic_year_id']
                )
                ->where(
                    'school_class_id',
                    $validated['school_class_id']
                )
                ->whereNotIn(
                    'subject_id',
                    $selectedSubjects
                )
                ->delete();


            /*
            * Add/update selected subjects.
            */
            foreach ($selectedSubjects as $subjectId) {

                $subject = Subject::where(
                        'school_id',
                        $schoolId
                    )
                    ->findOrFail($subjectId);


                ClassSubject::updateOrCreate(

                    [
                        'school_id' => $schoolId,

                        'academic_year_id' =>
                            $validated['academic_year_id'],

                        'school_class_id' =>
                            $validated['school_class_id'],

                        'subject_id' => $subjectId,
                    ],

                    [
                        'is_optional' =>
                            $request->boolean(
                                'optional.' . $subjectId
                            ),

                        'sort_order' =>
                            $request->input(
                                'sort_order.' . $subjectId,
                                $subject->sort_order ?? 0
                            ),

                        'status' => true,
                    ]
                );
            }

        });

        return redirect()
            ->route('class-subjects.index', [
                'academic_year_id' =>
                    $validated['academic_year_id'],

                'school_class_id' =>
                    $validated['school_class_id'],
            ])
            ->with(
                'success',
                'Subjects assigned successfully.'
            );
    }


    public function destroy(ClassSubject $classSubject)
    {
        $this->checkSchoolAccess($classSubject);

        /*
         * Later we will add dependency checks here
         * for timetable, marks, attendance etc.
         */

        $classSubject->delete();

        return back()->with(
            'success',
            'Subject removed from class successfully.'
        );
    }


    private function checkSchoolAccess(
        ClassSubject $classSubject
    ): void {

        $user = auth()->user();

        if (
            !$user->hasRole('Super Admin') &&
            $classSubject->school_id !== $user->school_id
        ) {
            abort(403);
        }

        if (
            $user->hasRole('Super Admin') &&
            $user->school_id &&
            $classSubject->school_id !== $user->school_id
        ) {
            abort(403);
        }
    }

    public function mappedSubjects(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id' => 'required|integer',
        ]);

        $mappings = ClassSubject::where('school_id', $schoolId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->get([
                'subject_id',
                'is_optional',
                'sort_order'
            ]);

        return response()->json([
            'subjects' => $mappings
        ]);
    }
}