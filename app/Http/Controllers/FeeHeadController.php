<?php

namespace App\Http\Controllers;

use App\Models\FeeHead;
use App\Models\FeeCycle;
use App\Models\FeeComponentGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FeeHeadController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = FeeHead::with([
                'componentGroup',
                'feeCycle',
            ])
            ->where('school_id', $schoolId);

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

        if ($request->filled('group_id')) {

            $query->where(
                'fee_component_group_id',
                $request->group_id
            );
        }

        if ($request->filled('cycle_id')) {

            $query->where(
                'fee_cycle_id',
                $request->cycle_id
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

        $feeHeads = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $groups = FeeComponentGroup::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $cycles = FeeCycle::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'fee-heads.index',
            compact(
                'feeHeads',
                'groups',
                'cycles'
            )
        );
    }


    public function create()
    {
        $schoolId = Auth::user()->school_id;

        $groups = FeeComponentGroup::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $cycles = FeeCycle::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'fee-heads.create',
            compact('groups', 'cycles')
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        $cycle = FeeCycle::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['fee_cycle_id']
            )
            ->where('status', 1)
            ->firstOrFail();

        FeeComponentGroup::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['fee_component_group_id']
            )
            ->where('status', 1)
            ->firstOrFail();

        FeeHead::create([
            'school_id' => $schoolId,

            'fee_component_group_id' =>
                $validated['fee_component_group_id'],

            'fee_cycle_id' =>
                $cycle->id,

            'name' =>
                trim($validated['name']),

            'code' =>
                !empty($validated['code'])
                    ? strtoupper(
                        trim($validated['code'])
                    )
                    : null,

            /*
             * Keep legacy frequency synchronized
             * until old installment generator
             * is replaced.
             */
            'frequency' =>
                $this->legacyFrequency(
                    $cycle->cycle_type
                ),

            'is_optional' =>
                $request->boolean(
                    'is_optional'
                ),

            'is_refundable' =>
                $request->boolean(
                    'is_refundable'
                ),

            'allow_concession' =>
                $request->boolean(
                    'allow_concession'
                ),

            'allow_waiver' =>
                $request->boolean(
                    'allow_waiver'
                ),

            'description' =>
                $validated['description']
                    ?? null,

            'sort_order' =>
                $validated['sort_order']
                    ?? 0,

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route('fee-heads.index')
            ->with(
                'success',
                'Fee Component created successfully.'
            );
    }


    public function edit(FeeHead $feeHead)
    {
        $this->authorizeSchool($feeHead);

        $schoolId = Auth::user()->school_id;

        /*
         * Include currently selected inactive
         * group/cycle too.
         */
        $groups = FeeComponentGroup::where(
                'school_id',
                $schoolId
            )
            ->where(function ($query) use ($feeHead) {

                $query->where('status', 1);

                if (
                    $feeHead->fee_component_group_id
                ) {
                    $query->orWhere(
                        'id',
                        $feeHead
                            ->fee_component_group_id
                    );
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $cycles = FeeCycle::where(
                'school_id',
                $schoolId
            )
            ->where(function ($query) use ($feeHead) {

                $query->where('status', 1);

                if ($feeHead->fee_cycle_id) {
                    $query->orWhere(
                        'id',
                        $feeHead->fee_cycle_id
                    );
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'fee-heads.edit',
            compact(
                'feeHead',
                'groups',
                'cycles'
            )
        );
    }


    public function update(
        Request $request,
        FeeHead $feeHead
    ) {
        $this->authorizeSchool($feeHead);

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $feeHead->id
        );

        $cycle = FeeCycle::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['fee_cycle_id']
            )
            ->firstOrFail();

        FeeComponentGroup::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['fee_component_group_id']
            )
            ->firstOrFail();

        $feeHead->update([
            'fee_component_group_id' =>
                $validated['fee_component_group_id'],

            'fee_cycle_id' =>
                $cycle->id,

            'name' =>
                trim($validated['name']),

            'code' =>
                !empty($validated['code'])
                    ? strtoupper(
                        trim($validated['code'])
                    )
                    : null,

            'frequency' =>
                $this->legacyFrequency(
                    $cycle->cycle_type
                ),

            'is_optional' =>
                $request->boolean(
                    'is_optional'
                ),

            'is_refundable' =>
                $request->boolean(
                    'is_refundable'
                ),

            'allow_concession' =>
                $request->boolean(
                    'allow_concession'
                ),

            'allow_waiver' =>
                $request->boolean(
                    'allow_waiver'
                ),

            'description' =>
                $validated['description']
                    ?? null,

            'sort_order' =>
                $validated['sort_order']
                    ?? 0,

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route('fee-heads.index')
            ->with(
                'success',
                'Fee Component updated successfully.'
            );
    }


    public function destroy(FeeHead $feeHead)
    {
        $this->authorizeSchool($feeHead);

        if (
            $feeHead
                ->structureItems()
                ->exists()
        ) {
            return back()->with(
                'error',
                'This Fee Component is already used in a Fee Template and cannot be deleted.'
            );
        }

        $feeHead->delete();

        return redirect()
            ->route('fee-heads.index')
            ->with(
                'success',
                'Fee Component deleted successfully.'
            );
    }


    private function validateData(
        Request $request,
        int $schoolId,
        ?int $ignoreId = null
    ): array {

        return $request->validate([

            'fee_component_group_id' => [
                'required',
                'integer',
                Rule::exists(
                    'fee_component_groups',
                    'id'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'fee_cycle_id' => [
                'required',
                'integer',
                Rule::exists(
                    'fee_cycles',
                    'id'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique(
                    'fee_heads',
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
                    'fee_heads',
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


    private function legacyFrequency(
        string $cycleType
    ): string {

        return match ($cycleType) {

            'monthly' =>
                'monthly',

            'quarterly' =>
                'quarterly',

            'half_yearly' =>
                'half_yearly',

            'annual' =>
                'annual',

            'one_time' =>
                'one_time',

            /*
             * Current old database enum does
             * not support "custom".
             *
             * Custom cycles will be handled
             * properly when Fee Templates
             * are upgraded.
             */
            'custom' =>
                'one_time',

            default =>
                'one_time',
        };
    }


    private function authorizeSchool(
        FeeHead $feeHead
    ): void {

        abort_unless(
            (int) $feeHead->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }
}