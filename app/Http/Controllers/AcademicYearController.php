<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = AcademicYear::query()
            ->with('school')
            ->orderBy('start_date', 'desc');

        // Normal school users can only see their own school.
        if (!$user->hasRole('Super Admin')) {
            $query->where('school_id', $user->school_id);
        }

        $academicYears = $query->paginate(20);

        return view(
            'academic-years.index',
            compact('academicYears')
        );
    }

    public function create()
    {
        return view('academic-years.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'is_current' => [
                'nullable',
                'boolean',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ]);

        if (!$user->school_id) {
            return back()
                ->withInput()
                ->withErrors([
                    'school' => 'No school is assigned to this user.',
                ]);
        }

        DB::transaction(function () use ($request, $user) {

            $isCurrent = $request->boolean('is_current');

            if ($isCurrent) {
                AcademicYear::where(
                    'school_id',
                    $user->school_id
                )->update([
                    'is_current' => false
                ]);
            }

            AcademicYear::create([
                'school_id' => $user->school_id,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_current' => $isCurrent,
                'status' => $request->boolean('status'),
            ]);
        });

        return redirect()
            ->route('academic-years.index')
            ->with(
                'success',
                'Academic year created successfully.'
            );
    }

    public function show(AcademicYear $academicYear)
    {
        $this->checkSchoolAccess($academicYear);

        return view(
            'academic-years.show',
            compact('academicYear')
        );
    }

    public function edit(AcademicYear $academicYear)
    {
        $this->checkSchoolAccess($academicYear);

        return view(
            'academic-years.edit',
            compact('academicYear')
        );
    }

    public function update(
        Request $request,
        AcademicYear $academicYear
    ) {
        $this->checkSchoolAccess($academicYear);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'is_current' => [
                'nullable',
                'boolean',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ]);

        DB::transaction(
            function () use ($request, $academicYear) {

                $isCurrent = $request->boolean('is_current');

                if ($isCurrent) {
                    AcademicYear::where(
                        'school_id',
                        $academicYear->school_id
                    )
                    ->where('id', '!=', $academicYear->id)
                    ->update([
                        'is_current' => false
                    ]);
                }

                $academicYear->update([
                    'name' => $request->name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'is_current' => $isCurrent,
                    'status' => $request->boolean('status'),
                ]);
            }
        );

        return redirect()
            ->route('academic-years.index')
            ->with(
                'success',
                'Academic year updated successfully.'
            );
    }

    public function destroy(AcademicYear $academicYear)
    {
        $this->checkSchoolAccess($academicYear);

        // Avoid deleting years already being used.
        if (
            $academicYear->sections()->exists() ||
            $academicYear->classSubjects()->exists() ||
            $academicYear->sectionGroups()->exists() ||
            $academicYear->calendarEvents()->exists()
        ) {
            return back()->with(
                'error',
                'This academic year is already in use and cannot be deleted.'
            );
        }

        $academicYear->delete();

        return redirect()
            ->route('academic-years.index')
            ->with(
                'success',
                'Academic year deleted successfully.'
            );
    }

    private function checkSchoolAccess(
        AcademicYear $academicYear
    ): void {
        $user = auth()->user();

        if (
            !$user->hasRole('Super Admin') &&
            $academicYear->school_id !== $user->school_id
        ) {
            abort(403);
        }
    }
}