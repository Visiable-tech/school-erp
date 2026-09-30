<?php

namespace App\Http\Controllers;

use App\Models\FeeInstallment;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeeInstallmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Installment Setup
    |--------------------------------------------------------------------------
    */

    public function index(FeeStructure $feeStructure)
    {
        $this->authorizeStructure($feeStructure);

        $feeStructure->load([
            'academicYear',
            'schoolClass',
            'items.feeHead',
            'items.installments',
        ]);

        return view(
            'fee-installments.index',
            compact('feeStructure')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        FeeStructure $feeStructure,
        FeeStructureItem $feeStructureItem
    ) {
        $this->authorizeStructure($feeStructure);

        $this->authorizeItem(
            $feeStructure,
            $feeStructureItem
        );

        $feeStructure->load([
            'academicYear',
            'schoolClass',
        ]);

        $feeStructureItem->load('feeHead');

        return view(
            'fee-installments.create',
            compact(
                'feeStructure',
                'feeStructureItem'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        FeeStructure $feeStructure,
        FeeStructureItem $feeStructureItem
    ) {
        $this->authorizeStructure($feeStructure);

        $this->authorizeItem(
            $feeStructure,
            $feeStructureItem
        );

        $validated = $request->validate([

            'installment_name' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'fee_installments',
                    'installment_name'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'fee_structure_item_id',
                            $feeStructureItem->id
                        )
                ),
            ],

            'period_start' =>
                'nullable|date',

            'period_end' =>
                'nullable|date|after_or_equal:period_start',

            'due_date' =>
                'required|date',

            'amount' =>
                'required|numeric|min:0|max:9999999999.99',

            'sort_order' =>
                'nullable|integer|min:0',

            'status' =>
                'nullable|boolean',
        ]);


        FeeInstallment::create([

            'school_id' =>
                auth()->user()->school_id,

            'fee_structure_id' =>
                $feeStructure->id,

            'fee_structure_item_id' =>
                $feeStructureItem->id,

            'installment_name' =>
                $validated['installment_name'],

            'period_start' =>
                $validated['period_start'] ?? null,

            'period_end' =>
                $validated['period_end'] ?? null,

            'due_date' =>
                $validated['due_date'],

            'amount' =>
                $validated['amount'],

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),

        ]);


        return redirect()
            ->route(
                'fee-installments.index',
                $feeStructure
            )
            ->with(
                'success',
                'Fee installment created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        FeeInstallment $feeInstallment
    ) {
        $this->authorizeInstallment(
            $feeInstallment
        );

        $feeInstallment->load([
            'feeStructure.academicYear',
            'feeStructure.schoolClass',
            'feeStructureItem.feeHead',
        ]);

        return view(
            'fee-installments.edit',
            compact('feeInstallment')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        FeeInstallment $feeInstallment
    ) {
        $this->authorizeInstallment(
            $feeInstallment
        );

        $validated = $request->validate([

            'installment_name' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'fee_installments',
                    'installment_name'
                )
                    ->ignore($feeInstallment->id)
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'fee_structure_item_id',
                                $feeInstallment
                                    ->fee_structure_item_id
                            )
                    ),
            ],

            'period_start' =>
                'nullable|date',

            'period_end' =>
                'nullable|date|after_or_equal:period_start',

            'due_date' =>
                'required|date',

            'amount' =>
                'required|numeric|min:0|max:9999999999.99',

            'sort_order' =>
                'nullable|integer|min:0',

            'status' =>
                'nullable|boolean',
        ]);


        $feeInstallment->update([

            'installment_name' =>
                $validated['installment_name'],

            'period_start' =>
                $validated['period_start'] ?? null,

            'period_end' =>
                $validated['period_end'] ?? null,

            'due_date' =>
                $validated['due_date'],

            'amount' =>
                $validated['amount'],

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),

        ]);


        return redirect()
            ->route(
                'fee-installments.index',
                $feeInstallment->fee_structure_id
            )
            ->with(
                'success',
                'Fee installment updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(
        FeeInstallment $feeInstallment
    ) {
        $this->authorizeInstallment(
            $feeInstallment
        );

        $structureId =
            $feeInstallment->fee_structure_id;

        /*
         * Later we'll block deletion when this
         * installment has already been assigned
         * to students or paid.
         */

        $feeInstallment->delete();


        return redirect()
            ->route(
                'fee-installments.index',
                $structureId
            )
            ->with(
                'success',
                'Fee installment deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Standard Schedule
    |--------------------------------------------------------------------------
    */

    public function generate(
        Request $request,
        FeeStructure $feeStructure,
        FeeStructureItem $feeStructureItem
    ) {
        $this->authorizeStructure(
            $feeStructure
        );

        $this->authorizeItem(
            $feeStructure,
            $feeStructureItem
        );

        $validated = $request->validate([
            'due_day' => [
                'required',
                'integer',
                'min:1',
                'max:28',
            ],
        ]);

        $feeStructure->load(
            'academicYear'
        );

        $feeStructureItem->load(
            'feeHead.feeCycle'
        );

        $academicYear =
            $feeStructure->academicYear;

        if (!$academicYear) {

            return back()->with(
                'error',
                'Academic Year not found.'
            );
        }

        $feeHead =
            $feeStructureItem->feeHead;

        if (!$feeHead) {

            return back()->with(
                'error',
                'Fee Component not found.'
            );
        }

        $feeCycle =
            $feeHead->feeCycle;

        if (!$feeCycle) {

            return back()->with(
                'error',
                'Fee Cycle is not assigned to "' .
                $feeHead->name .
                '". Please configure the Fee Component first.'
            );
        }

        /*
        * Don't regenerate after student dues
        * have started using this schedule.
        */
        $hasUsedInstallments =
            FeeInstallment::where(
                'fee_structure_item_id',
                $feeStructureItem->id
            )
            ->whereHas('studentDues')
            ->exists();

        if ($hasUsedInstallments) {

            return back()->with(
                'error',
                'This schedule is already used in student fee dues and cannot be regenerated.'
            );
        }

        DB::transaction(function () use (
            $feeStructure,
            $feeStructureItem,
            $academicYear,
            $feeCycle,
            $validated
        ) {

            /*
            * Existing unused schedule can safely
            * be rebuilt.
            */
            FeeInstallment::where(
                'fee_structure_item_id',
                $feeStructureItem->id
            )->delete();


            $yearStart =
                $academicYear->start_date
                    ->copy()
                    ->startOfDay();

            $yearEnd =
                $academicYear->end_date
                    ->copy()
                    ->endOfDay();


            /*
            * Determine cycle interval.
            */
            $intervalMonths = match (
                $feeCycle->cycle_type
            ) {
                'monthly' => 1,
                'quarterly' => 3,
                'half_yearly' => 6,
                'annual' => 12,
                'one_time' => null,
                'custom' => null,
                default => null,
            };


            /*
            * Custom cycles should not be guessed.
            * They can be manually configured using
            * your existing Add Installment screen.
            */
            if (
                $feeCycle->cycle_type === 'custom'
            ) {
                throw new \RuntimeException(
                    'Custom Fee Cycle schedules must be created manually.'
                );
            }


            /*
            * One-time charge.
            */
            if (
                $feeCycle->cycle_type === 'one_time'
            ) {

                $periodStart =
                    $yearStart->copy();

                $periodEnd =
                    $yearEnd->copy();

                $dueDate =
                    $periodStart->copy()
                        ->day(
                            min(
                                $validated['due_day'],
                                $periodStart->daysInMonth
                            )
                        );

                FeeInstallment::create([

                    'school_id' =>
                        auth()->user()->school_id,

                    'fee_structure_id' =>
                        $feeStructure->id,

                    'fee_structure_item_id' =>
                        $feeStructureItem->id,

                    'installment_name' =>
                        'One Time',

                    'period_start' =>
                        $periodStart->toDateString(),

                    'period_end' =>
                        $periodEnd->toDateString(),

                    'due_date' =>
                        $dueDate->toDateString(),

                    'amount' =>
                        $feeStructureItem->amount,

                    'sort_order' => 1,

                    'status' => true,
                ]);

                return;
            }


            /*
            * Monthly / Quarterly /
            * Half-Yearly / Annual
            */
            $cursor =
                $yearStart->copy();

            $installmentNo = 1;

            while ($cursor->lte($yearEnd)) {

                $periodStart =
                    $cursor->copy();

                $periodEnd =
                    $cursor->copy()
                        ->addMonths($intervalMonths)
                        ->subDay();

                if ($periodEnd->gt($yearEnd)) {
                    $periodEnd =
                        $yearEnd->copy();
                }


                /*
                * Due date belongs to the first
                * month of this cycle.
                */
                $dueDate =
                    $periodStart->copy()
                        ->day(
                            min(
                                $validated['due_day'],
                                $periodStart->daysInMonth
                            )
                        );


                /*
                * Better human-readable names.
                */
                $installmentName = match (
                    $feeCycle->cycle_type
                ) {

                    'monthly' =>
                        $periodStart->format(
                            'M Y'
                        ),

                    'quarterly' =>
                        'Quarter ' .
                        $installmentNo .
                        ' (' .
                        $periodStart->format('M Y') .
                        ')',

                    'half_yearly' =>
                        'Half Year ' .
                        $installmentNo .
                        ' (' .
                        $periodStart->format('M Y') .
                        ')',

                    'annual' =>
                        'Annual ' .
                        $periodStart->format('Y'),

                    default =>
                        'Installment ' .
                        $installmentNo,
                };


                FeeInstallment::create([

                    'school_id' =>
                        auth()->user()->school_id,

                    'fee_structure_id' =>
                        $feeStructure->id,

                    'fee_structure_item_id' =>
                        $feeStructureItem->id,

                    'installment_name' =>
                        $installmentName,

                    'period_start' =>
                        $periodStart->toDateString(),

                    'period_end' =>
                        $periodEnd->toDateString(),

                    'due_date' =>
                        $dueDate->toDateString(),

                    /*
                    * Fee Template amount means
                    * amount PER cycle.
                    */
                    'amount' =>
                        $feeStructureItem->amount,

                    'sort_order' =>
                        $installmentNo,

                    'status' => true,
                ]);


                $cursor =
                    $cursor->copy()
                        ->addMonths(
                            $intervalMonths
                        );

                $installmentNo++;
            }
        });


        return redirect()
            ->route(
                'fee-installments.index',
                $feeStructure
            )
            ->with(
                'success',
                'Fee Cycle schedule generated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */

    private function authorizeStructure(
        FeeStructure $feeStructure
    ): void {

        abort_unless(
            (int) $feeStructure->school_id ===
            (int) auth()->user()->school_id,
            403
        );
    }


    private function authorizeItem(
        FeeStructure $feeStructure,
        FeeStructureItem $item
    ): void {

        abort_unless(
            (int) $item->fee_structure_id ===
            (int) $feeStructure->id,
            404
        );
    }


    private function authorizeInstallment(
        FeeInstallment $installment
    ): void {

        abort_unless(
            (int) $installment->school_id ===
            (int) auth()->user()->school_id,
            403
        );
    }
}