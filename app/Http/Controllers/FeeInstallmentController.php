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

        $feeStructure->load('academicYear');

        $feeStructureItem->load('feeHead');


        $validated = $request->validate([

            'due_day' =>
                'required|integer|min:1|max:28',

        ]);


        if (
            $feeStructureItem
                ->installments()
                ->exists()
        ) {

            return back()->with(
                'error',
                'Installments already exist for this Fee Head. Delete or edit the existing schedule first.'
            );
        }


        $year =
            $feeStructure->academicYear;

        if (!$year) {

            return back()->with(
                'error',
                'Academic Year not found.'
            );
        }


        $start =
            $year->start_date->copy()
                ->startOfMonth();

        $end =
            $year->end_date->copy()
                ->startOfMonth();

        $frequency =
            $feeStructureItem
                ->feeHead
                ->frequency;


        DB::transaction(function () use (
            $feeStructure,
            $feeStructureItem,
            $start,
            $end,
            $frequency,
            $validated
        ) {

            $months = [];

            $cursor = $start->copy();

            while ($cursor <= $end) {

                $months[] =
                    $cursor->copy();

                $cursor->addMonth();
            }


            /*
            |--------------------------------------------------------------------------
            | Determine installment months
            |--------------------------------------------------------------------------
            */

            switch ($frequency) {

                case 'monthly':

                    $scheduleMonths =
                        $months;

                    break;


                case 'quarterly':

                    $scheduleMonths =
                        collect($months)
                            ->values()
                            ->filter(
                                fn ($month, $index) =>
                                    $index % 3 === 0
                            )
                            ->values()
                            ->all();

                    break;


                case 'half_yearly':

                    $scheduleMonths =
                        collect($months)
                            ->values()
                            ->filter(
                                fn ($month, $index) =>
                                    $index % 6 === 0
                            )
                            ->values()
                            ->all();

                    break;


                case 'annual':

                case 'one_time':

                    $scheduleMonths = [
                        $start->copy()
                    ];

                    break;


                default:

                    $scheduleMonths = [];
            }


            /*
            |--------------------------------------------------------------------------
            | Create installments
            |--------------------------------------------------------------------------
            */

            foreach (
                $scheduleMonths
                as $index => $month
            ) {

                /*
                 * For monthly:
                 * each installment = configured amount.
                 *
                 * Quarterly/half-yearly/etc. also use the
                 * configured Fee Head amount per installment.
                 */

                $dueDate =
                    $month->copy()
                        ->day(
                            min(
                                $validated['due_day'],
                                $month->daysInMonth
                            )
                        );


                $name =
                    $month->format('M Y');


                FeeInstallment::create([

                    'school_id' =>
                        auth()->user()->school_id,

                    'fee_structure_id' =>
                        $feeStructure->id,

                    'fee_structure_item_id' =>
                        $feeStructureItem->id,

                    'installment_name' =>
                        $name,

                    'period_start' =>
                        $month->copy()
                            ->startOfMonth()
                            ->toDateString(),

                    'period_end' =>
                        $month->copy()
                            ->endOfMonth()
                            ->toDateString(),

                    'due_date' =>
                        $dueDate->toDateString(),

                    'amount' =>
                        $feeStructureItem->amount,

                    'sort_order' =>
                        $index + 1,

                    'status' =>
                        true,

                ]);
            }

        });


        return redirect()
            ->route(
                'fee-installments.index',
                $feeStructure
            )
            ->with(
                'success',
                'Fee schedule generated successfully.'
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