<?php

namespace App\Http\Controllers;

use App\Models\FeeComponentGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FeeComponentGroupController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = FeeComponentGroup::where(
            'school_id',
            $schoolId
        );

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                );
            });
        }

        if (
            $request->filled('status') &&
            in_array(
                $request->status,
                ['0', '1'],
                true
            )
        ) {
            $query->where(
                'status',
                $request->status
            );
        }

        $groups = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'fee-component-groups.index',
            compact('groups')
        );
    }


    public function create()
    {
        return view(
            'fee-component-groups.create'
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        FeeComponentGroup::create([
            'school_id' => $schoolId,

            'name' => trim(
                $validated['name']
            ),

            'code' => !empty($validated['code'])
                ? strtoupper(
                    trim($validated['code'])
                )
                : null,

            'description' =>
                $validated['description'] ?? null,

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route(
                'fee-component-groups.index'
            )
            ->with(
                'success',
                'Fee Component Group created successfully.'
            );
    }


    public function edit(
        FeeComponentGroup $feeComponentGroup
    ) {
        $this->authorizeSchool(
            $feeComponentGroup
        );

        return view(
            'fee-component-groups.edit',
            compact('feeComponentGroup')
        );
    }


    public function update(
        Request $request,
        FeeComponentGroup $feeComponentGroup
    ) {
        $this->authorizeSchool(
            $feeComponentGroup
        );

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $feeComponentGroup->id
        );

        $feeComponentGroup->update([
            'name' => trim(
                $validated['name']
            ),

            'code' => !empty($validated['code'])
                ? strtoupper(
                    trim($validated['code'])
                )
                : null,

            'description' =>
                $validated['description'] ?? null,

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route(
                'fee-component-groups.index'
            )
            ->with(
                'success',
                'Fee Component Group updated successfully.'
            );
    }


    public function destroy(
        FeeComponentGroup $feeComponentGroup
    ) {
        $this->authorizeSchool(
            $feeComponentGroup
        );

        if (
            $feeComponentGroup
                ->feeComponents()
                ->exists()
        ) {
            return back()->with(
                'error',
                'This Fee Component Group is already used by Fee Components and cannot be deleted.'
            );
        }

        $feeComponentGroup->delete();

        return redirect()
            ->route('fee-component-groups.index')
            ->with(
                'success',
                'Fee Component Group deleted successfully.'
            );
    }


    private function validateData(
        Request $request,
        int $schoolId,
        ?int $ignoreId = null
    ): array {

        return $request->validate([

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique(
                    'fee_component_groups',
                    'name'
                )
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'school_id',
                                $schoolId
                            )
                    )
                    ->ignore($ignoreId),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique(
                    'fee_component_groups',
                    'code'
                )
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'school_id',
                                $schoolId
                            )
                    )
                    ->ignore($ignoreId),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);
    }


    private function authorizeSchool(
        FeeComponentGroup $feeComponentGroup
    ): void {

        abort_unless(
            (int) $feeComponentGroup->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }
}