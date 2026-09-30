<?php

namespace App\Http\Controllers;

use App\Models\BankMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BankMasterController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = BankMaster::where(
            'school_id',
            $schoolId
        );

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'bank_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'short_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'bank_code',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'branch_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'ifsc_code',
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

        $banks = $query
            ->orderBy('sort_order')
            ->orderBy('bank_name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'bank-masters.index',
            compact('banks')
        );
    }


    public function create()
    {
        return view(
            'bank-masters.create'
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        BankMaster::create([
            'school_id' => $schoolId,

            'bank_name' =>
                trim($validated['bank_name']),

            'short_name' =>
                $this->nullableTrim(
                    $validated['short_name'] ?? null
                ),

            'bank_code' =>
                $this->nullableUpper(
                    $validated['bank_code'] ?? null
                ),

            'branch_name' =>
                $this->nullableTrim(
                    $validated['branch_name'] ?? null
                ),

            'branch_code' =>
                $this->nullableUpper(
                    $validated['branch_code'] ?? null
                ),

            'ifsc_code' =>
                $this->nullableUpper(
                    $validated['ifsc_code'] ?? null
                ),

            'micr_code' =>
                $this->nullableUpper(
                    $validated['micr_code'] ?? null
                ),

            'address' =>
                $this->nullableTrim(
                    $validated['address'] ?? null
                ),

            'city' =>
                $this->nullableTrim(
                    $validated['city'] ?? null
                ),

            'contact_person' =>
                $this->nullableTrim(
                    $validated['contact_person'] ?? null
                ),

            'phone' =>
                $this->nullableTrim(
                    $validated['phone'] ?? null
                ),

            'email' =>
                $validated['email'] ?? null,

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route('bank-masters.index')
            ->with(
                'success',
                'Bank created successfully.'
            );
    }


    public function edit(
        BankMaster $bankMaster
    ) {
        $this->authorizeSchool(
            $bankMaster
        );

        return view(
            'bank-masters.edit',
            compact('bankMaster')
        );
    }


    public function update(
        Request $request,
        BankMaster $bankMaster
    ) {
        $this->authorizeSchool(
            $bankMaster
        );

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $bankMaster->id
        );

        $bankMaster->update([

            'bank_name' =>
                trim($validated['bank_name']),

            'short_name' =>
                $this->nullableTrim(
                    $validated['short_name'] ?? null
                ),

            'bank_code' =>
                $this->nullableUpper(
                    $validated['bank_code'] ?? null
                ),

            'branch_name' =>
                $this->nullableTrim(
                    $validated['branch_name'] ?? null
                ),

            'branch_code' =>
                $this->nullableUpper(
                    $validated['branch_code'] ?? null
                ),

            'ifsc_code' =>
                $this->nullableUpper(
                    $validated['ifsc_code'] ?? null
                ),

            'micr_code' =>
                $this->nullableUpper(
                    $validated['micr_code'] ?? null
                ),

            'address' =>
                $this->nullableTrim(
                    $validated['address'] ?? null
                ),

            'city' =>
                $this->nullableTrim(
                    $validated['city'] ?? null
                ),

            'contact_person' =>
                $this->nullableTrim(
                    $validated['contact_person'] ?? null
                ),

            'phone' =>
                $this->nullableTrim(
                    $validated['phone'] ?? null
                ),

            'email' =>
                $validated['email'] ?? null,

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route('bank-masters.index')
            ->with(
                'success',
                'Bank updated successfully.'
            );
    }


    public function destroy(
        BankMaster $bankMaster
    ) {
        $this->authorizeSchool(
            $bankMaster
        );

        if (
            $bankMaster
                ->schoolAccounts()
                ->exists()
        ) {
            return back()->with(
                'error',
                'This bank is used by one or more School Accounts and cannot be deleted.'
            );
        }

        $bankMaster->delete();

        return redirect()
            ->route('bank-masters.index')
            ->with(
                'success',
                'Bank deleted successfully.'
            );
    }


    private function validateData(
        Request $request,
        int $schoolId,
        ?int $ignoreId = null
    ): array {

        return $request->validate([

            'bank_name' => [
                'required',
                'string',
                'max:150',
            ],

            'short_name' => [
                'nullable',
                'string',
                'max:50',
            ],

            'bank_code' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique(
                    'bank_masters',
                    'bank_code'
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

            'branch_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'branch_code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'ifsc_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'micr_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:150',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);
    }


    private function authorizeSchool(
        BankMaster $bankMaster
    ): void {

        abort_unless(
            (int) $bankMaster->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }


    private function nullableTrim(
        ?string $value
    ): ?string {

        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value !== ''
            ? $value
            : null;
    }


    private function nullableUpper(
        ?string $value
    ): ?string {

        $value = $this->nullableTrim(
            $value
        );

        return $value !== null
            ? strtoupper($value)
            : null;
    }
}