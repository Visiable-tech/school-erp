<?php

namespace App\Http\Controllers;

use App\Models\ConcessionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ConcessionTypeController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = ConcessionType::where(
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

        if ($request->filled('mode')) {

            $query->where(
                'concession_mode',
                $request->mode
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

        $concessionTypes = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'concession-types.index',
            compact('concessionTypes')
        );
    }


    public function create()
    {
        return view(
            'concession-types.create'
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        ConcessionType::create([
            'school_id' => $schoolId,

            'name' => trim(
                $validated['name']
            ),

            'code' => $this->formatCode(
                $validated['code'] ?? null
            ),

            'concession_mode' =>
                $validated['concession_mode'],

            'default_value' =>
                $validated['default_value']
                ?? null,

            'maximum_amount' =>
                $validated['maximum_amount']
                ?? null,

            'requires_approval' =>
                $request->boolean(
                    'requires_approval'
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
                'concession-types.index'
            )
            ->with(
                'success',
                'Concession Type created successfully.'
            );
    }


    public function edit(
        ConcessionType $concessionType
    ) {
        $this->authorizeSchool(
            $concessionType
        );

        return view(
            'concession-types.edit',
            compact('concessionType')
        );
    }


    public function update(
        Request $request,
        ConcessionType $concessionType
    ) {
        $this->authorizeSchool(
            $concessionType
        );

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $concessionType->id
        );

        $concessionType->update([
            'name' => trim(
                $validated['name']
            ),

            'code' => $this->formatCode(
                $validated['code'] ?? null
            ),

            'concession_mode' =>
                $validated['concession_mode'],

            'default_value' =>
                $validated['default_value']
                ?? null,

            'maximum_amount' =>
                $validated['maximum_amount']
                ?? null,

            'requires_approval' =>
                $request->boolean(
                    'requires_approval'
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
                'concession-types.index'
            )
            ->with(
                'success',
                'Concession Type updated successfully.'
            );
    }


    public function destroy(
        ConcessionType $concessionType
    ) {
        $this->authorizeSchool(
            $concessionType
        );

        /*
         * Once Concession Assignment is built,
         * we will block deletion if this type
         * has already been assigned to students.
         */

        $concessionType->delete();

        return redirect()
            ->route(
                'concession-types.index'
            )
            ->with(
                'success',
                'Concession Type deleted successfully.'
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
                    'concession_types',
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
                    'concession_types',
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

            'concession_mode' => [
                'required',
                Rule::in([
                    'fixed',
                    'percentage'
                ]),
            ],

            'default_value' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'maximum_amount' => [
                'nullable',
                'numeric',
                'min:0',
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
        ConcessionType $concessionType
    ): void {

        abort_unless(
            (int) $concessionType->school_id ===
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