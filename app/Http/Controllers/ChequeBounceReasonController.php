<?php

namespace App\Http\Controllers;

use App\Models\ChequeBounceReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChequeBounceReasonController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = ChequeBounceReason::where(
            'school_id',
            $schoolId
        );

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

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

        $reasons = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'cheque-bounce-reasons.index',
            compact('reasons')
        );
    }


    public function create()
    {
        return view(
            'cheque-bounce-reasons.create'
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        ChequeBounceReason::create([
            'school_id' => $schoolId,

            'name' => trim(
                $validated['name']
            ),

            'code' => $this->formatCode(
                $validated['code'] ?? null
            ),

            'bounce_charge' =>
                $request->boolean('apply_charge')
                    ? ($validated['bounce_charge'] ?? 0)
                    : 0,

            'apply_charge' =>
                $request->boolean(
                    'apply_charge'
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
            ->route(
                'cheque-bounce-reasons.index'
            )
            ->with(
                'success',
                'Cheque Bounce Reason created successfully.'
            );
    }


    public function edit(
        ChequeBounceReason $chequeBounceReason
    ) {
        $this->authorizeSchool(
            $chequeBounceReason
        );

        return view(
            'cheque-bounce-reasons.edit',
            compact('chequeBounceReason')
        );
    }


    public function update(
        Request $request,
        ChequeBounceReason $chequeBounceReason
    ) {
        $this->authorizeSchool(
            $chequeBounceReason
        );

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $chequeBounceReason->id
        );

        $chequeBounceReason->update([
            'name' => trim(
                $validated['name']
            ),

            'code' => $this->formatCode(
                $validated['code'] ?? null
            ),

            'bounce_charge' =>
                $request->boolean('apply_charge')
                    ? ($validated['bounce_charge'] ?? 0)
                    : 0,

            'apply_charge' =>
                $request->boolean(
                    'apply_charge'
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
            ->route(
                'cheque-bounce-reasons.index'
            )
            ->with(
                'success',
                'Cheque Bounce Reason updated successfully.'
            );
    }


    public function destroy(
        ChequeBounceReason $chequeBounceReason
    ) {
        $this->authorizeSchool(
            $chequeBounceReason
        );

        /*
         * When Cheque/DD transactions are built,
         * deletion will be blocked if this reason
         * has already been used.
         */

        $chequeBounceReason->delete();

        return redirect()
            ->route(
                'cheque-bounce-reasons.index'
            )
            ->with(
                'success',
                'Cheque Bounce Reason deleted successfully.'
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
                    'cheque_bounce_reasons',
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
                    'cheque_bounce_reasons',
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

            'bounce_charge' => [
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
        ChequeBounceReason $reason
    ): void {

        abort_unless(
            (int) $reason->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }


    private function formatCode(
        ?string $code
    ): ?string {

        if (!$code) {
            return null;
        }

        return strtoupper(
            trim($code)
        );
    }
}