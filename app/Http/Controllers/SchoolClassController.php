<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Wing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $classes = SchoolClass::with('wing')
            ->where('school_id', $schoolId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('school-classes.index', compact('classes'));
    }

    public function create()
    {
        $schoolId = auth()->user()->school_id;

        $wings = Wing::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('school-classes.create', compact('wings'));
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
            'wing_id' => [
                'required',
                Rule::exists('wings', 'id')
                    ->where(fn ($query) =>
                        $query->where('school_id', $schoolId)
                    ),
            ],

            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique('school_classes')
                    ->where(fn ($query) =>
                        $query->where('school_id', $schoolId)
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:30',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        SchoolClass::create([
            'school_id' => $schoolId,
            'wing_id' => $validated['wing_id'],
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('school-classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(SchoolClass $schoolClass)
    {
        $this->checkSchoolAccess($schoolClass);

        $wings = Wing::where(
                'school_id',
                $schoolClass->school_id
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'school-classes.edit',
            compact('schoolClass', 'wings')
        );
    }

    public function update(
        Request $request,
        SchoolClass $schoolClass
    ) {
        $this->checkSchoolAccess($schoolClass);

        $validated = $request->validate([
            'wing_id' => [
                'required',

                Rule::exists('wings', 'id')
                    ->where(fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolClass->school_id
                        )
                    ),
            ],

            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique('school_classes')
                    ->where(fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolClass->school_id
                        )
                    )
                    ->ignore($schoolClass->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:30',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $schoolClass->update([
            'wing_id' => $validated['wing_id'],
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('school-classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $schoolClass)
    {
        $this->checkSchoolAccess($schoolClass);

        if ($schoolClass->sections()->exists()) {
            return back()->with(
                'error',
                'This class already has sections and cannot be deleted.'
            );
        }

        if ($schoolClass->classSubjects()->exists()) {
            return back()->with(
                'error',
                'Subjects are already assigned to this class.'
            );
        }

        $schoolClass->delete();

        return redirect()
            ->route('school-classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    private function checkSchoolAccess(
        SchoolClass $schoolClass
    ): void {
        $user = auth()->user();

        if (
            !$user->hasRole('Super Admin') &&
            $schoolClass->school_id !== $user->school_id
        ) {
            abort(403);
        }

        if (
            $user->hasRole('Super Admin') &&
            $user->school_id &&
            $schoolClass->school_id !== $user->school_id
        ) {
            abort(403);
        }
    }
}