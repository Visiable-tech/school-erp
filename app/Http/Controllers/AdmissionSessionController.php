<?php

namespace App\Http\Controllers;

use App\Models\AdmissionSession;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdmissionSessionController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $sessions = AdmissionSession::with('academicYear')
            ->where('school_id', $schoolId)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->paginate(20);

        return view(
            'admission-sessions.index',
            compact('sessions')
        );
    }


    public function create()
    {
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        return view(
            'admission-sessions.create',
            compact('academicYears')
        );
    }


    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([

            'academic_year_id' => [
                'required',

                Rule::exists('academic_years', 'id')
                    ->where(
                        fn ($query) =>
                        $query->where('school_id', $schoolId)
                    ),
            ],

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('admission_sessions', 'name')
                    ->where(
                        fn ($query) =>
                        $query
                            ->where('school_id', $schoolId)
                            ->where(
                                'academic_year_id',
                                $request->academic_year_id
                            )
                    ),
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

        ]);


        DB::transaction(function () use (
            $validated,
            $request,
            $schoolId
        ) {

            $isCurrent =
                $request->boolean('is_current');

            if ($isCurrent) {

                AdmissionSession::where(
                    'school_id',
                    $schoolId
                )->update([
                    'is_current' => false
                ]);
            }


            AdmissionSession::create([

                'school_id' =>
                    $schoolId,

                'academic_year_id' =>
                    $validated['academic_year_id'],

                'name' =>
                    $validated['name'],

                'start_date' =>
                    $validated['start_date'] ?? null,

                'end_date' =>
                    $validated['end_date'] ?? null,

                'is_current' =>
                    $isCurrent,

                'status' =>
                    $request->boolean('status'),

            ]);

        });


        return redirect()
            ->route('admission-sessions.index')
            ->with(
                'success',
                'Admission session created successfully.'
            );
    }


    public function edit(
        AdmissionSession $admissionSession
    ) {
        $this->checkSchoolAccess(
            $admissionSession
        );

        $academicYears = AcademicYear::where(
                'school_id',
                $admissionSession->school_id
            )
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        return view(
            'admission-sessions.edit',
            compact(
                'admissionSession',
                'academicYears'
            )
        );
    }


    public function update(
        Request $request,
        AdmissionSession $admissionSession
    ) {
        $this->checkSchoolAccess(
            $admissionSession
        );

        $schoolId =
            $admissionSession->school_id;


        $validated = $request->validate([

            'academic_year_id' => [
                'required',

                Rule::exists('academic_years', 'id')
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolId
                        )
                    ),
            ],

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique(
                    'admission_sessions',
                    'name'
                )
                    ->where(
                        fn ($query) =>
                        $query
                            ->where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'academic_year_id',
                                $request->academic_year_id
                            )
                    )
                    ->ignore(
                        $admissionSession->id
                    ),
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

        ]);


        DB::transaction(function () use (
            $validated,
            $request,
            $admissionSession,
            $schoolId
        ) {

            $isCurrent =
                $request->boolean('is_current');


            if ($isCurrent) {

                AdmissionSession::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        '!=',
                        $admissionSession->id
                    )
                    ->update([
                        'is_current' => false
                    ]);
            }


            $admissionSession->update([

                'academic_year_id' =>
                    $validated['academic_year_id'],

                'name' =>
                    $validated['name'],

                'start_date' =>
                    $validated['start_date'] ?? null,

                'end_date' =>
                    $validated['end_date'] ?? null,

                'is_current' =>
                    $isCurrent,

                'status' =>
                    $request->boolean('status'),

            ]);

        });


        return redirect()
            ->route('admission-sessions.index')
            ->with(
                'success',
                'Admission session updated successfully.'
            );
    }


    public function destroy(
        AdmissionSession $admissionSession
    ) {
        $this->checkSchoolAccess(
            $admissionSession
        );


        if (
            $admissionSession
                ->enquiries()
                ->exists()
        ) {

            return back()->with(
                'error',
                'This admission session already contains enquiries and cannot be deleted.'
            );
        }


        $admissionSession->delete();


        return redirect()
            ->route('admission-sessions.index')
            ->with(
                'success',
                'Admission session deleted successfully.'
            );
    }


    private function checkSchoolAccess(
        AdmissionSession $admissionSession
    ): void {

        if (
            $admissionSession->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }
}