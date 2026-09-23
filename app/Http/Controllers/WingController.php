<?php

namespace App\Http\Controllers;

use App\Models\Wing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WingController extends Controller
{
    public function index()
    {
        $query = Wing::with('school')
            ->orderBy('sort_order')
            ->orderBy('name');

        if (!auth()->user()->hasRole('Super Admin')) {
            $query->where('school_id', auth()->user()->school_id);
        } elseif (auth()->user()->school_id) {
            // Temporary until we build Super Admin school switching.
            $query->where('school_id', auth()->user()->school_id);
        }

        $wings = $query->paginate(20);

        return view('wings.index', compact('wings'));
    }

    public function create()
    {
        return view('wings.create');
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
                'max:100',
                Rule::unique('wings')
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

        Wing::create([
            'school_id' => $schoolId,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('wings.index')
            ->with('success', 'Wing created successfully.');
    }

    public function edit(Wing $wing)
    {
        $this->checkSchoolAccess($wing);

        return view('wings.edit', compact('wing'));
    }

    public function update(Request $request, Wing $wing)
    {
        $this->checkSchoolAccess($wing);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique('wings')
                    ->where(fn ($query) =>
                        $query->where(
                            'school_id',
                            $wing->school_id
                        )
                    )
                    ->ignore($wing->id),
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

        $wing->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('wings.index')
            ->with('success', 'Wing updated successfully.');
    }

    public function destroy(Wing $wing)
    {
        $this->checkSchoolAccess($wing);

        if ($wing->classes()->exists()) {
            return back()->with(
                'error',
                'This wing is already assigned to classes and cannot be deleted.'
            );
        }

        $wing->delete();

        return redirect()
            ->route('wings.index')
            ->with('success', 'Wing deleted successfully.');
    }

    private function checkSchoolAccess(Wing $wing): void
    {
        $user = auth()->user();

        if (
            !$user->hasRole('Super Admin') &&
            $wing->school_id !== $user->school_id
        ) {
            abort(403);
        }

        // Temporary until school switching is implemented.
        if (
            $user->hasRole('Super Admin') &&
            $user->school_id &&
            $wing->school_id !== $user->school_id
        ) {
            abort(403);
        }
    }
}