<?php

namespace App\Http\Controllers;

use App\Models\FeeHead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeeHeadController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = FeeHead::where('school_id', $schoolId);

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");

            });
        }

        if ($request->filled('frequency')) {

            $query->where(
                'frequency',
                $request->frequency
            );
        }

        if ($request->filled('status')) {

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

        return view(
            'fee-heads.index',
            compact('feeHeads')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('fee-heads.create');
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('fee_heads', 'name')
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique('fee_heads', 'code')
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    ),
            ],

            'frequency' => [
                'required',
                Rule::in([
                    'one_time',
                    'monthly',
                    'quarterly',
                    'half_yearly',
                    'annual',
                ]),
            ],

            'sort_order' =>
                'nullable|integer|min:0',

            'is_optional' =>
                'nullable|boolean',

            'status' =>
                'nullable|boolean',
        ]);


        FeeHead::create([

            'school_id' =>
                $schoolId,

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'] ?: null,

            'frequency' =>
                $validated['frequency'],

            'is_optional' =>
                $request->boolean('is_optional'),

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),

        ]);


        return redirect()
            ->route('fee-heads.index')
            ->with(
                'success',
                'Fee Head created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(FeeHead $feeHead)
    {
        $this->authorizeSchool($feeHead);

        return view(
            'fee-heads.edit',
            compact('feeHead')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        FeeHead $feeHead
    ) {
        $this->authorizeSchool($feeHead);

        $schoolId = auth()->user()->school_id;


        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('fee_heads', 'name')
                    ->ignore($feeHead->id)
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique('fee_heads', 'code')
                    ->ignore($feeHead->id)
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    ),
            ],

            'frequency' => [
                'required',
                Rule::in([
                    'one_time',
                    'monthly',
                    'quarterly',
                    'half_yearly',
                    'annual',
                ]),
            ],

            'sort_order' =>
                'nullable|integer|min:0',

            'is_optional' =>
                'nullable|boolean',

            'status' =>
                'nullable|boolean',

        ]);


        $feeHead->update([

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'] ?: null,

            'frequency' =>
                $validated['frequency'],

            'is_optional' =>
                $request->boolean('is_optional'),

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),

        ]);


        return redirect()
            ->route('fee-heads.index')
            ->with(
                'success',
                'Fee Head updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(FeeHead $feeHead)
    {
        $this->authorizeSchool($feeHead);

        if ($feeHead->structureItems()->exists()) {

            return back()->with(
                'error',
                'This Fee Head is already used in a Fee Structure and cannot be deleted.'
            );
        }

        $feeHead->delete();

        return redirect()
            ->route('fee-heads.index')
            ->with(
                'success',
                'Fee Head deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | School Security
    |--------------------------------------------------------------------------
    */

    private function authorizeSchool(FeeHead $feeHead): void
    {
        abort_unless(
            (int) $feeHead->school_id ===
            (int) auth()->user()->school_id,
            403
        );
    }
}