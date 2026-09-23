<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $subjects = Subject::where('school_id', $schoolId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('subjects.index', compact('subjects'));
    }


    public function create()
    {
        return view('subjects.create');
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

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('subjects', 'name')
                    ->where(
                        fn ($query) =>
                        $query->where('school_id', $schoolId)
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'type' => [
                'required',
                Rule::in([
                    'theory',
                    'practical',
                    'activity'
                ]),
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);

        Subject::create([
            'school_id' => $schoolId,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'type' => $validated['type'],
            'is_optional' => $request->boolean('is_optional'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('subjects.index')
            ->with(
                'success',
                'Subject created successfully.'
            );
    }


    public function edit(Subject $subject)
    {
        $this->checkSchoolAccess($subject);

        return view(
            'subjects.edit',
            compact('subject')
        );
    }


    public function update(
        Request $request,
        Subject $subject
    ) {
        $this->checkSchoolAccess($subject);

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('subjects', 'name')
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'school_id',
                            $subject->school_id
                        )
                    )
                    ->ignore($subject->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'type' => [
                'required',
                Rule::in([
                    'theory',
                    'practical',
                    'activity'
                ]),
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);

        $subject->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'type' => $validated['type'],
            'is_optional' => $request->boolean('is_optional'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('subjects.index')
            ->with(
                'success',
                'Subject updated successfully.'
            );
    }


    public function destroy(Subject $subject)
    {
        $this->checkSchoolAccess($subject);

        /*
         * Do not delete subjects which are
         * already mapped with classes.
         */
        if ($subject->classSubjects()->exists()) {

            return back()->with(
                'error',
                'This subject is already assigned to a class and cannot be deleted.'
            );
        }

        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with(
                'success',
                'Subject deleted successfully.'
            );
    }


    private function checkSchoolAccess(
        Subject $subject
    ): void {

        $user = auth()->user();

        if (
            !$user->hasRole('Super Admin') &&
            $subject->school_id !== $user->school_id
        ) {
            abort(403);
        }

        if (
            $user->hasRole('Super Admin') &&
            $user->school_id &&
            $subject->school_id !== $user->school_id
        ) {
            abort(403);
        }
    }
}