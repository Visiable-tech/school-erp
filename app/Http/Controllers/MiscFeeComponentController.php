<?php

namespace App\Http\Controllers;

use App\Models\MiscFeeComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MiscFeeComponentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = MiscFeeComponent::where(
            'school_id',
            $schoolId
        );

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(
                function ($q) use ($search) {

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
                }
            );
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

        $components = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'misc-fee-components.index',
            compact('components')
        );
    }


    public function create()
    {
        return view(
            'misc-fee-components.create'
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        MiscFeeComponent::create([
            'school_id' => $schoolId,

            'name' => trim(
                $validated['name']
            ),

            'code' => !empty(
                $validated['code']
            )
                ? strtoupper(
                    trim($validated['code'])
                )
                : null,

            'fixed_amount' =>
                $request->boolean(
                    'fixed_amount'
                ),

            'default_amount' =>
                $request->boolean(
                    'fixed_amount'
                )
                    ? $validated[
                        'default_amount'
                    ]
                    : null,

            'is_refundable' =>
                $request->boolean(
                    'is_refundable'
                ),

            'allow_concession' =>
                $request->boolean(
                    'allow_concession'
                ),

            'description' =>
                $validated['description']
                    ?? null,

            'sort_order' =>
                $validated['sort_order']
                    ?? 0,

            'status' =>
                $request->boolean(
                    'status'
                ),
        ]);

        return redirect()
            ->route(
                'misc-fee-components.index'
            )
            ->with(
                'success',
                'Misc. Component created successfully.'
            );
    }


    public function edit(
        MiscFeeComponent $miscFeeComponent
    ) {
        $this->authorizeSchool(
            $miscFeeComponent
        );

        return view(
            'misc-fee-components.edit',
            compact('miscFeeComponent')
        );
    }


    public function update(
        Request $request,
        MiscFeeComponent $miscFeeComponent
    ) {
        $this->authorizeSchool(
            $miscFeeComponent
        );

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $miscFeeComponent->id
        );

        $miscFeeComponent->update([
            'name' => trim(
                $validated['name']
            ),

            'code' => !empty(
                $validated['code']
            )
                ? strtoupper(
                    trim($validated['code'])
                )
                : null,

            'fixed_amount' =>
                $request->boolean(
                    'fixed_amount'
                ),

            'default_amount' =>
                $request->boolean(
                    'fixed_amount'
                )
                    ? $validated[
                        'default_amount'
                    ]
                    : null,

            'is_refundable' =>
                $request->boolean(
                    'is_refundable'
                ),

            'allow_concession' =>
                $request->boolean(
                    'allow_concession'
                ),

            'description' =>
                $validated['description']
                    ?? null,

            'sort_order' =>
                $validated['sort_order']
                    ?? 0,

            'status' =>
                $request->boolean(
                    'status'
                ),
        ]);

        return redirect()
            ->route(
                'misc-fee-components.index'
            )
            ->with(
                'success',
                'Misc. Component updated successfully.'
            );
    }


    public function destroy(
        MiscFeeComponent $miscFeeComponent
    ) {
        $this->authorizeSchool(
            $miscFeeComponent
        );

        /*
         * Later, once Misc. Collection is built,
         * add transaction/assignment protection here.
         */

        $miscFeeComponent->delete();

        return redirect()
            ->route(
                'misc-fee-components.index'
            )
            ->with(
                'success',
                'Misc. Component deleted successfully.'
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
                    'misc_fee_components',
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
                    'misc_fee_components',
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

            'default_amount' => [
                Rule::requiredIf(
                    $request->boolean(
                        'fixed_amount'
                    )
                ),
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
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
        MiscFeeComponent $miscFeeComponent
    ): void {

        abort_unless(
            (int) $miscFeeComponent
                ->school_id ===
            (int) Auth::user()
                ->school_id,
            403
        );
    }
}