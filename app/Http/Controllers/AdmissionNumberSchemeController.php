<?php

namespace App\Http\Controllers;

use App\Models\AdmissionNumberScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class AdmissionNumberSchemeController extends Controller
{
    private function schoolId(): int
    {
        $schoolId = Auth::user()?->school_id;

        abort_if(!$schoolId, 403, 'School is not assigned.');

        return (int) $schoolId;
    }

    private function initializeSchemes(): void
    {
        $schoolId = $this->schoolId();

        foreach (AdmissionNumberScheme::TYPES as $type => $name) {
            DB::table('admission_number_schemes')->insertOrIgnore([
                'school_id' => $schoolId,
                'scheme_type' => $type,
                'scheme_name' => $name,
                'auto_generate' => 1,
                'include_academic_year' => 0,
                'include_class' => 0,
                'separator' => '/',
                'starting_number' => 1,
                'last_generated_number' => 0,
                'number_length' => 5,
                'allow_manual_entry' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function findScheme(int $id): AdmissionNumberScheme
    {
        return AdmissionNumberScheme::where('school_id', $this->schoolId())
            ->findOrFail($id);
    }

    public function index()
    {
        $this->initializeSchemes();

        $schemes = AdmissionNumberScheme::where('school_id', $this->schoolId())
            ->get()
            ->sortBy(fn ($scheme) =>
                array_search(
                    $scheme->scheme_type,
                    array_keys(AdmissionNumberScheme::TYPES),
                    true
                )
            );

        return view('admission.number-schemes.index', compact('schemes'));
    }

    public function edit(int $id)
    {
        $scheme = $this->findScheme($id);

        return view('admission.number-schemes.edit', compact('scheme'));
    }

    public function update(Request $request, int $id)
    {
        $scheme = $this->findScheme($id);

        $validated = $request->validate([
            'auto_generate' => ['required', 'boolean'],
            'include_academic_year' => ['required', 'boolean'],
            'include_class' => ['required', 'boolean'],
            'separator' => ['nullable', Rule::in(['/', '-', '_', ''])],
            'fixed_string' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/'],
            'starting_number' => ['required', 'integer', 'min:1'],
            'number_length' => ['required', 'integer', 'between:1,12'],
            'allow_manual_entry' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
        ]);

        if (
            !$validated['auto_generate']
            && !$validated['allow_manual_entry']
            && $validated['status']
        ) {
            return back()
                ->withErrors([
                    'auto_generate' => 'Enable automatic generation or manual entry.',
                ])
                ->withInput();
        }

        if (
            $scheme->last_generated_number > 0
            && (int) $validated['starting_number'] !== (int) $scheme->starting_number
        ) {
            return back()
                ->withErrors([
                    'starting_number' => 'Starting number cannot be changed after numbers have been generated.',
                ])
                ->withInput();
        }

        if (
            $validated['starting_number']
            <= $scheme->last_generated_number
            && $scheme->last_generated_number > 0
        ) {
            return back()
                ->withErrors([
                    'starting_number' => 'Starting number must be greater than the last generated number.',
                ])
                ->withInput();
        }

        // Prevent changing the format after a number has been issued.
        if ($scheme->last_generated_number > 0) {
            foreach ([
                'include_academic_year',
                'include_class',
                'separator',
                'fixed_string',
                'number_length',
            ] as $field) {
                if ((string) $validated[$field] !== (string) $scheme->$field) {
                    return back()
                        ->withErrors([
                            $field => 'Number format cannot be changed after issuing numbers.',
                        ])
                        ->withInput();
                }
            }
        }

        $scheme->update($validated);

        return redirect()
            ->route('admission-number-schemes.index')
            ->with('success', 'Number Generation Scheme updated successfully.');
    }
}