<?php

namespace App\Http\Controllers;

use App\Models\AdmissionCommunicationMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdmissionCommunicationModeController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = AdmissionCommunicationMode::where(
            'school_id',
            $schoolId
        );

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");

            });
        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                (int) $request->status
            );
        }

        $communicationModes = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admission.communication-modes.index',
            compact('communicationModes')
        );
    }


    public function create()
    {
        return view(
            'admission.communication-modes.create'
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

                Rule::unique(
                    'admission_communication_modes',
                    'name'
                )->where(
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
                'max:30',
            ],

            'description' => [
                'nullable',
                'string',
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


        AdmissionCommunicationMode::create([

            'school_id' =>
                $schoolId,

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'] ?? null,

            'description' =>
                $validated['description'] ?? null,

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $validated['status'],

            'created_by' =>
                Auth::id(),
        ]);


        return redirect()
            ->route(
                'admission-communication-modes.index'
            )
            ->with(
                'success',
                'Communication Mode created successfully.'
            );
    }


    public function edit(
        AdmissionCommunicationMode $admissionCommunicationMode
    ) {
        $this->checkSchool(
            $admissionCommunicationMode
        );

        return view(
            'admission.communication-modes.edit',
            compact('admissionCommunicationMode')
        );
    }


    public function update(
        Request $request,
        AdmissionCommunicationMode $admissionCommunicationMode
    ) {
        $this->checkSchool(
            $admissionCommunicationMode
        );

        $schoolId = Auth::user()->school_id;


        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'admission_communication_modes',
                    'name'
                )
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolId
                        )
                    )
                    ->ignore(
                        $admissionCommunicationMode->id
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:30',
            ],

            'description' => [
                'nullable',
                'string',
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


        $admissionCommunicationMode->update([

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'] ?? null,

            'description' =>
                $validated['description'] ?? null,

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $validated['status'],
        ]);


        return redirect()
            ->route(
                'admission-communication-modes.index'
            )
            ->with(
                'success',
                'Communication Mode updated successfully.'
            );
    }


    public function destroy(
        AdmissionCommunicationMode $admissionCommunicationMode
    ) {
        $this->checkSchool(
            $admissionCommunicationMode
        );

        $admissionCommunicationMode->delete();

        return redirect()
            ->route(
                'admission-communication-modes.index'
            )
            ->with(
                'success',
                'Communication Mode deleted successfully.'
            );
    }


    private function checkSchool(
        AdmissionCommunicationMode $admissionCommunicationMode
    ): void {

        abort_if(
            $admissionCommunicationMode->school_id
                != Auth::user()->school_id,
            403
        );
    }
}