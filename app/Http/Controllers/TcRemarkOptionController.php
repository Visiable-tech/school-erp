<?php

namespace App\Http\Controllers;

use App\Models\TcRemarkOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TcRemarkOptionController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $remarks = TcRemarkOption::where('school_id', $schoolId)

            ->when($request->search, function ($query, $search) {

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
            })

            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where(
                        'status',
                        $request->status
                    )
            )

            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'students.tc-remark-options.index',
            compact('remarks')
        );
    }


    public function create()
    {
        return view(
            'students.tc-remark-options.create'
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',

                Rule::unique('tc_remark_options')
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


        TcRemarkOption::create([

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
            ->route('tc-remark-options.index')
            ->with(
                'success',
                'T.C. Remark Option created successfully.'
            );
    }


    public function edit(
        TcRemarkOption $tcRemarkOption
    ) {
        $this->authorizeSchool(
            $tcRemarkOption
        );

        return view(
            'students.tc-remark-options.edit',
            compact('tcRemarkOption')
        );
    }


    public function update(
        Request $request,
        TcRemarkOption $tcRemarkOption
    ) {
        $this->authorizeSchool(
            $tcRemarkOption
        );

        $schoolId =
            Auth::user()->school_id;


        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',

                Rule::unique('tc_remark_options')
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'school_id',
                                $schoolId
                            )
                    )
                    ->ignore(
                        $tcRemarkOption->id
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


        $tcRemarkOption->update([

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
            ->route('tc-remark-options.index')
            ->with(
                'success',
                'T.C. Remark Option updated successfully.'
            );
    }


    public function destroy(
        TcRemarkOption $tcRemarkOption
    ) {
        $this->authorizeSchool(
            $tcRemarkOption
        );

        /*
         * Once Transfer Certificates are implemented,
         * we will prevent deletion when this option
         * has already been used.
         */

        $tcRemarkOption->delete();


        return redirect()
            ->route('tc-remark-options.index')
            ->with(
                'success',
                'T.C. Remark Option deleted successfully.'
            );
    }


    private function authorizeSchool(
        TcRemarkOption $tcRemarkOption
    ): void {

        abort_unless(
            (int) $tcRemarkOption->school_id
                ===
            (int) Auth::user()->school_id,
            403
        );
    }
}