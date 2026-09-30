<?php

namespace App\Http\Controllers;

use App\Models\FeeCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FeeCycleController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = FeeCycle::where(
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

        if ($request->filled('cycle_type')) {

            $query->where(
                'cycle_type',
                $request->cycle_type
            );
        }

        if (
            $request->filled('status')
            && in_array(
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

        $feeCycles = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'fee-cycles.index',
            compact('feeCycles')
        );
    }


    public function create()
    {
        return view('fee-cycles.create');
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        FeeCycle::create([
            'school_id' => $schoolId,

            'name' =>
                trim($validated['name']),

            'code' =>
                !empty($validated['code'])
                    ? strtoupper(
                        trim($validated['code'])
                    )
                    : null,

            'cycle_type' =>
                $validated['cycle_type'],

            'installments_count' =>
                $validated['installments_count'],

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route('fee-cycles.index')
            ->with(
                'success',
                'Fee Cycle created successfully.'
            );
    }


    public function edit(FeeCycle $feeCycle)
    {
        $this->authorizeSchool($feeCycle);

        return view(
            'fee-cycles.edit',
            compact('feeCycle')
        );
    }


    public function update(
        Request $request,
        FeeCycle $feeCycle
    ) {
        $this->authorizeSchool($feeCycle);

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $feeCycle->id
        );

        $feeCycle->update([
            'name' =>
                trim($validated['name']),

            'code' =>
                !empty($validated['code'])
                    ? strtoupper(
                        trim($validated['code'])
                    )
                    : null,

            'cycle_type' =>
                $validated['cycle_type'],

            'installments_count' =>
                $validated['installments_count'],

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route('fee-cycles.index')
            ->with(
                'success',
                'Fee Cycle updated successfully.'
            );
    }


    public function destroy(FeeCycle $feeCycle)
    {
        $this->authorizeSchool($feeCycle);

        if ($feeCycle->feeComponents()->exists()) {

            return back()->with(
                'error',
                'This Fee Cycle is already used by Fee Components and cannot be deleted.'
            );
        }

        $feeCycle->delete();

        return redirect()
            ->route('fee-cycles.index')
            ->with(
                'success',
                'Fee Cycle deleted successfully.'
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
                'max:100',

                Rule::unique('fee_cycles', 'name')
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
                'max:30',

                Rule::unique('fee_cycles', 'code')
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'school_id',
                                $schoolId
                            )
                    )
                    ->ignore($ignoreId),
            ],

            'cycle_type' => [
                'required',
                Rule::in([
                    'monthly',
                    'quarterly',
                    'half_yearly',
                    'annual',
                    'one_time',
                    'custom',
                ]),
            ],

            'installments_count' => [
                'required',
                'integer',
                'min:1',
                'max:24',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);
    }


    private function authorizeSchool(
        FeeCycle $feeCycle
    ): void {

        abort_unless(
            (int) $feeCycle->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }
}