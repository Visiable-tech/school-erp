<?php

namespace App\Http\Controllers;

use App\Models\PromotionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PromotionStatusController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $statuses = PromotionStatus::where('school_id', $schoolId)

            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%');
                });
            })

            ->when(
                $request->filled('type'),
                fn ($query) =>
                    $query->where('type', $request->type)
            )

            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where('status', $request->status)
            )

            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'students.promotion-statuses.index',
            compact('statuses')
        );
    }


    public function create()
    {
        return view(
            'students.promotion-statuses.create'
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique('promotion_statuses')
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'type' => [
                'required',
                Rule::in([
                    'promoted',
                    'repeated',
                    'detained',
                    'conditional',
                    'other',
                ]),
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

        PromotionStatus::create([
            'school_id'   => $schoolId,
            'name'        => trim($validated['name']),
            'code'        => !empty($validated['code'])
                                ? trim($validated['code'])
                                : null,
            'type'        => $validated['type'],
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('promotion-statuses.index')
            ->with(
                'success',
                'Promotion Status created successfully.'
            );
    }


    public function edit(PromotionStatus $promotionStatus)
    {
        $this->authorizeSchool($promotionStatus);

        return view(
            'students.promotion-statuses.edit',
            compact('promotionStatus')
        );
    }


    public function update(
        Request $request,
        PromotionStatus $promotionStatus
    ) {
        $this->authorizeSchool($promotionStatus);

        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique('promotion_statuses')
                    ->where(
                        fn ($query) =>
                            $query->where('school_id', $schoolId)
                    )
                    ->ignore($promotionStatus->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'type' => [
                'required',
                Rule::in([
                    'promoted',
                    'repeated',
                    'detained',
                    'conditional',
                    'other',
                ]),
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

        $promotionStatus->update([
            'name'        => trim($validated['name']),
            'code'        => !empty($validated['code'])
                                ? trim($validated['code'])
                                : null,
            'type'        => $validated['type'],
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('promotion-statuses.index')
            ->with(
                'success',
                'Promotion Status updated successfully.'
            );
    }


    public function destroy(PromotionStatus $promotionStatus)
    {
        $this->authorizeSchool($promotionStatus);

        /*
         * When Promotions/Repetitions is connected,
         * we will block deletion if this status
         * has already been used.
         */

        $promotionStatus->delete();

        return redirect()
            ->route('promotion-statuses.index')
            ->with(
                'success',
                'Promotion Status deleted successfully.'
            );
    }


    private function authorizeSchool(
        PromotionStatus $promotionStatus
    ): void {
        abort_unless(
            (int) $promotionStatus->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }
}