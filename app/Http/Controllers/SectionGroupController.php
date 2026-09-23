<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\SectionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SectionGroupController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $query = SectionGroup::with([
                'academicYear',
                'sections.schoolClass.wing'
            ])
            ->withCount('sections')
            ->where('school_id', $schoolId);

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        $sectionGroups = $query
            ->orderByDesc('academic_year_id')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'section-groups.index',
            compact(
                'sectionGroups',
                'academicYears'
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

        /*
         * Sections are loaded by AJAX after
         * selecting Academic Year.
         */
        return view(
            'section-groups.create',
            compact('academicYears')
        );
    }


    public function getSections(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([
            'academic_year_id' => [
                'required',

                Rule::exists('academic_years', 'id')
                    ->where(
                        fn ($query) =>
                        $query->where('school_id', $schoolId)
                    ),
            ],
        ]);

        $sections = Section::with([
                'schoolClass.wing'
            ])
            ->where('school_id', $schoolId)
            ->where(
                'academic_year_id',
                $request->academic_year_id
            )
            ->where('status', 1)
            ->orderBy('school_class_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        $data = $sections->map(function ($section) {

            return [
                'id' => $section->id,

                'section_name' => $section->name,

                'class_name' =>
                    $section->schoolClass?->name ?? '',

                'wing_name' =>
                    $section->schoolClass?->wing?->name ?? '',
            ];

        });


        return response()->json([
            'sections' => $data
        ]);
    }


    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        if (!$schoolId) {
            return back()
                ->withInput()
                ->withErrors([
                    'school' =>
                        'No school is assigned to this user.'
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

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('section_groups', 'name')
                    ->where(
                        fn ($query) =>
                        $query
                            ->where('school_id', $schoolId)
                            ->where(
                                'academic_year_id',
                                $request->academic_year_id
                            )
                    ),
            ],

            'sections' => [
                'required',
                'array',
                'min:1',
            ],

            'sections.*' => [
                'integer',

                Rule::exists('sections', 'id')
                    ->where(
                        fn ($query) =>
                        $query
                            ->where('school_id', $schoolId)
                            ->where(
                                'academic_year_id',
                                $request->academic_year_id
                            )
                    ),
            ],

        ]);


        DB::transaction(function () use (
            $validated,
            $request,
            $schoolId
        ) {

            $sectionGroup = SectionGroup::create([
                'school_id' => $schoolId,

                'academic_year_id' =>
                    $validated['academic_year_id'],

                'name' => $validated['name'],

                'status' =>
                    $request->boolean('status'),
            ]);


            $sectionGroup
                ->sections()
                ->sync($validated['sections']);

        });


        return redirect()
            ->route('section-groups.index')
            ->with(
                'success',
                'Section group created successfully.'
            );
    }


    public function edit(SectionGroup $sectionGroup)
    {
        $this->checkSchoolAccess($sectionGroup);

        $academicYears = AcademicYear::where(
                'school_id',
                $sectionGroup->school_id
            )
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();


        $sectionGroup->load('sections');


        return view(
            'section-groups.edit',
            compact(
                'sectionGroup',
                'academicYears'
            )
        );
    }


    public function update(
        Request $request,
        SectionGroup $sectionGroup
    ) {
        $this->checkSchoolAccess($sectionGroup);

        $schoolId = $sectionGroup->school_id;


        $validated = $request->validate([

            'academic_year_id' => [
                'required',

                Rule::exists('academic_years', 'id')
                    ->where(
                        fn ($query) =>
                        $query->where('school_id', $schoolId)
                    ),
            ],

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('section_groups', 'name')
                    ->where(
                        fn ($query) =>
                        $query
                            ->where('school_id', $schoolId)
                            ->where(
                                'academic_year_id',
                                $request->academic_year_id
                            )
                    )
                    ->ignore($sectionGroup->id),
            ],

            'sections' => [
                'required',
                'array',
                'min:1',
            ],

            'sections.*' => [
                'integer',

                Rule::exists('sections', 'id')
                    ->where(
                        fn ($query) =>
                        $query
                            ->where('school_id', $schoolId)
                            ->where(
                                'academic_year_id',
                                $request->academic_year_id
                            )
                    ),
            ],

        ]);


        DB::transaction(function () use (
            $validated,
            $request,
            $sectionGroup
        ) {

            $sectionGroup->update([

                'academic_year_id' =>
                    $validated['academic_year_id'],

                'name' =>
                    $validated['name'],

                'status' =>
                    $request->boolean('status'),

            ]);


            $sectionGroup
                ->sections()
                ->sync($validated['sections']);

        });


        return redirect()
            ->route('section-groups.index')
            ->with(
                'success',
                'Section group updated successfully.'
            );
    }


    public function destroy(
        SectionGroup $sectionGroup
    ) {
        $this->checkSchoolAccess($sectionGroup);

        DB::transaction(function () use ($sectionGroup) {

            $sectionGroup->sections()->detach();

            $sectionGroup->delete();

        });


        return redirect()
            ->route('section-groups.index')
            ->with(
                'success',
                'Section group deleted successfully.'
            );
    }


    private function checkSchoolAccess(
        SectionGroup $sectionGroup
    ): void {

        $user = auth()->user();

        if (
            !$user->hasRole('Super Admin') &&
            $sectionGroup->school_id !== $user->school_id
        ) {
            abort(403);
        }


        if (
            $user->hasRole('Super Admin') &&
            $user->school_id &&
            $sectionGroup->school_id !== $user->school_id
        ) {
            abort(403);
        }
    }
}