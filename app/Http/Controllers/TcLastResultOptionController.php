<?php

namespace App\Http\Controllers;

use App\Models\TcLastResultOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TcLastResultOptionController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $results = TcLastResultOption::where('school_id', $schoolId)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'students.tc-last-result-options.index',
            compact('results')
        );
    }

    public function create()
    {
        return view(
            'students.tc-last-result-options.create'
        );
    }

    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('tc_last_result_options')
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

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);

        TcLastResultOption::create([
            'school_id'   => $schoolId,
            'name'        => trim($validated['name']),
            'code'        => !empty($validated['code'])
                                ? trim($validated['code'])
                                : null,
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('tc-last-result-options.index')
            ->with(
                'success',
                'T.C. Last Result Option created successfully.'
            );
    }

    public function edit(
        TcLastResultOption $tcLastResultOption
    ) {
        $this->authorizeSchool($tcLastResultOption);

        return view(
            'students.tc-last-result-options.edit',
            compact('tcLastResultOption')
        );
    }

    public function update(
        Request $request,
        TcLastResultOption $tcLastResultOption
    ) {
        $this->authorizeSchool($tcLastResultOption);

        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('tc_last_result_options')
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    )
                    ->ignore($tcLastResultOption->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $tcLastResultOption->update([
            'name'        => trim($validated['name']),
            'code'        => !empty($validated['code'])
                                ? trim($validated['code'])
                                : null,
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('tc-last-result-options.index')
            ->with(
                'success',
                'T.C. Last Result Option updated successfully.'
            );
    }

    public function destroy(
        TcLastResultOption $tcLastResultOption
    ) {
        $this->authorizeSchool($tcLastResultOption);

        // When the TC module is connected,
        // prevent deletion if already used.
        $tcLastResultOption->delete();

        return redirect()
            ->route('tc-last-result-options.index')
            ->with(
                'success',
                'T.C. Last Result Option deleted successfully.'
            );
    }

    private function authorizeSchool(
        TcLastResultOption $tcLastResultOption
    ): void {
        abort_unless(
            (int) $tcLastResultOption->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }
}