<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $sections = Section::with([
                'academicYear',
                'schoolClass.wing'
            ])
            ->where('school_id', $schoolId)
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('school_class_id')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('sections.index', compact('sections'));
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

        return view(
            'sections.create',
            compact('academicYears', 'classes')
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

            'name' => [
                'required',
                'string',
                'max:50',

                Rule::unique('sections')
                    ->where(
                        fn ($query) =>
                        $query
                            ->where('school_id', $schoolId)
                            ->where(
                                'academic_year_id',
                                $request->academic_year_id
                            )
                            ->where(
                                'school_class_id',
                                $request->school_class_id
                            )
                    ),
            ],

            'capacity' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);

        Section::create([
            'school_id' => $schoolId,
            'academic_year_id' => $validated['academic_year_id'],
            'school_class_id' => $validated['school_class_id'],
            'name' => $validated['name'],
            'capacity' => $validated['capacity'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('sections.index')
            ->with('success', 'Section created successfully.');
    }


    public function edit(Section $section)
    {
        $this->checkSchoolAccess($section);

        $academicYears = AcademicYear::where(
                'school_id',
                $section->school_id
            )
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::with('wing')
            ->where('school_id', $section->school_id)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'sections.edit',
            compact(
                'section',
                'academicYears',
                'classes'
            )
        );
    }


    public function update(
        Request $request,
        Section $section
    ) {
        $this->checkSchoolAccess($section);

        $validated = $request->validate([

            'academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'school_id',
                            $section->school_id
                        )
                    ),
            ],

            'school_class_id' => [
                'required',
                Rule::exists('school_classes', 'id')
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'school_id',
                            $section->school_id
                        )
                    ),
            ],

            'name' => [
                'required',
                'string',
                'max:50',

                Rule::unique('sections')
                    ->where(
                        fn ($query) =>
                        $query
                            ->where(
                                'school_id',
                                $section->school_id
                            )
                            ->where(
                                'academic_year_id',
                                $request->academic_year_id
                            )
                            ->where(
                                'school_class_id',
                                $request->school_class_id
                            )
                    )
                    ->ignore($section->id),
            ],

            'capacity' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);

        $section->update([
            'academic_year_id' => $validated['academic_year_id'],
            'school_class_id' => $validated['school_class_id'],
            'name' => $validated['name'],
            'capacity' => $validated['capacity'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('sections.index')
            ->with('success', 'Section updated successfully.');
    }


    public function destroy(Section $section)
    {
        $this->checkSchoolAccess($section);

        /*
         * Later, when Student module is created,
         * we will also check whether students are
         * assigned to this section.
         */

        if ($section->sectionGroups()->exists()) {

            return back()->with(
                'error',
                'This section is assigned to a section group and cannot be deleted.'
            );
        }

        $section->delete();

        return redirect()
            ->route('sections.index')
            ->with('success', 'Section deleted successfully.');
    }


    private function checkSchoolAccess(
        Section $section
    ): void {

        $user = auth()->user();

        if (
            !$user->hasRole('Super Admin') &&
            $section->school_id !== $user->school_id
        ) {
            abort(403);
        }

        if (
            $user->hasRole('Super Admin') &&
            $user->school_id &&
            $section->school_id !== $user->school_id
        ) {
            abort(403);
        }
    }
}