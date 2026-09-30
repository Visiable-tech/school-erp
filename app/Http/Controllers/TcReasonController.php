<?php

namespace App\Http\Controllers;

use App\Models\TcReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TcReasonController extends Controller
{
    /**
     * List
     */
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $reasons = TcReason::where('school_id', $schoolId)

            ->when(
                $request->search,
                function ($query, $search) {
                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'code',
                            'like',
                            '%' . $search . '%'
                        );
                    });
                }
            )

            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    $query->where(
                        'status',
                        $request->status
                    );
                }
            )

            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'students.tc-reasons.index',
            compact('reasons')
        );
    }


    /**
     * Create
     */
    public function create()
    {
        return view(
            'students.tc-reasons.create'
        );
    }


    /**
     * Store
     */
    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('tc_reasons')
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'school_id',
                                $schoolId
                            )
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);


        TcReason::create([
            'school_id' => $schoolId,

            'name' =>
                trim($validated['name']),

            'code' =>
                !empty($validated['code'])
                    ? trim($validated['code'])
                    : null,

            'description' =>
                $validated['description']
                ?? null,

            'sort_order' =>
                $validated['sort_order']
                ?? 0,

            'status' =>
                $validated['status'],
        ]);


        return redirect()
            ->route('tc-reasons.index')
            ->with(
                'success',
                'T.C. Reason created successfully.'
            );
    }


    /**
     * Edit
     */
    public function edit(TcReason $tcReason)
    {
        $this->authorizeSchool($tcReason);

        return view(
            'students.tc-reasons.edit',
            compact('tcReason')
        );
    }


    /**
     * Update
     */
    public function update(
        Request $request,
        TcReason $tcReason
    ) {
        $this->authorizeSchool($tcReason);

        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('tc_reasons')
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'school_id',
                                $schoolId
                            )
                    )
                    ->ignore($tcReason->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);


        $tcReason->update([
            'name' =>
                trim($validated['name']),

            'code' =>
                !empty($validated['code'])
                    ? trim($validated['code'])
                    : null,

            'description' =>
                $validated['description']
                ?? null,

            'sort_order' =>
                $validated['sort_order']
                ?? 0,

            'status' =>
                $validated['status'],
        ]);


        return redirect()
            ->route('tc-reasons.index')
            ->with(
                'success',
                'T.C. Reason updated successfully.'
            );
    }


    /**
     * Delete
     */
    public function destroy(TcReason $tcReason)
    {
        $this->authorizeSchool($tcReason);

        /*
         * At this stage T.C. Reason is not yet attached
         * to a Transfer Certificate.
         *
         * Later, when TC module is created, we will add
         * an "in use" check here.
         */

        $tcReason->delete();


        return redirect()
            ->route('tc-reasons.index')
            ->with(
                'success',
                'T.C. Reason deleted successfully.'
            );
    }


    /**
     * School security
     */
    private function authorizeSchool(
        TcReason $tcReason
    ): void {

        abort_unless(
            (int) $tcReason->school_id
                ===
            (int) Auth::user()->school_id,
            403
        );
    }
}