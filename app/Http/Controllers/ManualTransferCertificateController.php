<?php

namespace App\Http\Controllers;

use App\Models\TcLastResultOption;
use App\Models\TcReason;
use App\Models\TcRemarkOption;
use App\Models\TransferCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ManualTransferCertificateController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Manual TC List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = TransferCertificate::with([
                'reason',
                'remarkOption',
                'lastResultOption',
            ])
            ->where('school_id', $schoolId)
            ->where('is_manual', true);


        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'tc_number',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'manual_admission_no',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'manual_student_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'manual_father_name',
                    'like',
                    "%{$search}%"
                );
            });
        }


        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        $certificates = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'students.management.manual-transfer-certificate.index',
            compact('certificates')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $schoolId = auth()->user()->school_id;


        $tcReasons = TcReason::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        $tcRemarks = TcRemarkOption::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        $tcLastResults = TcLastResultOption::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        return view(
            'students.management.manual-transfer-certificate.create',
            compact(
                'tcReasons',
                'tcRemarks',
                'tcLastResults'
            )
        );
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

            'tc_number' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'transfer_certificates',
                    'tc_number'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'manual_admission_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'manual_student_name' => [
                'required',
                'string',
                'max:150',
            ],

            'manual_father_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'manual_mother_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'manual_date_of_birth' => [
                'nullable',
                'date',
            ],

            'manual_academic_year' => [
                'nullable',
                'string',
                'max:100',
            ],

            'manual_class' => [
                'required',
                'string',
                'max:100',
            ],

            'manual_section' => [
                'nullable',
                'string',
                'max:100',
            ],

            'application_date' => [
                'nullable',
                'date',
            ],

            'issue_date' => [
                'required',
                'date',
            ],

            'leaving_date' => [
                'nullable',
                'date',
            ],

            'tc_reason_id' => [
                'required',
                Rule::exists(
                    'tc_reasons',
                    'id'
                )->where(
                    fn ($query) =>
                        $query
                            ->where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'status',
                                1
                            )
                ),
            ],

            'tc_remark_option_id' => [
                'nullable',
                Rule::exists(
                    'tc_remark_options',
                    'id'
                )->where(
                    fn ($query) =>
                        $query
                            ->where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'status',
                                1
                            )
                ),
            ],

            'tc_last_result_option_id' => [
                'nullable',
                Rule::exists(
                    'tc_last_result_options',
                    'id'
                )->where(
                    fn ($query) =>
                        $query
                            ->where(
                                'school_id',
                                $schoolId
                            )
                            ->where(
                                'status',
                                1
                            )
                ),
            ],

            'conduct' => [
                'nullable',
                'string',
                'max:150',
            ],

            'next_school' => [
                'nullable',
                'string',
                'max:255',
            ],

            'next_class' => [
                'nullable',
                'string',
                'max:100',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],

        ]);


        $certificate = TransferCertificate::create([

            'school_id' =>
                $schoolId,

            /*
            |--------------------------------------------------------------------------
            | Manual TC
            |--------------------------------------------------------------------------
            */

            'is_manual' =>
                true,

            'student_id' =>
                null,

            'student_enrollment_id' =>
                null,


            /*
            |--------------------------------------------------------------------------
            | Student Details
            |--------------------------------------------------------------------------
            */

            'manual_admission_no' =>
                $validated[
                    'manual_admission_no'
                ] ?? null,

            'manual_student_name' =>
                $validated[
                    'manual_student_name'
                ],

            'manual_father_name' =>
                $validated[
                    'manual_father_name'
                ] ?? null,

            'manual_mother_name' =>
                $validated[
                    'manual_mother_name'
                ] ?? null,

            'manual_date_of_birth' =>
                $validated[
                    'manual_date_of_birth'
                ] ?? null,

            'manual_academic_year' =>
                $validated[
                    'manual_academic_year'
                ] ?? null,

            'manual_class' =>
                $validated[
                    'manual_class'
                ],

            'manual_section' =>
                $validated[
                    'manual_section'
                ] ?? null,


            /*
            |--------------------------------------------------------------------------
            | TC Details
            |--------------------------------------------------------------------------
            */

            'tc_number' =>
                $validated['tc_number'],

            'application_date' =>
                $validated[
                    'application_date'
                ] ?? null,

            'issue_date' =>
                $validated['issue_date'],

            'leaving_date' =>
                $validated[
                    'leaving_date'
                ] ?? null,

            'tc_reason_id' =>
                $validated[
                    'tc_reason_id'
                ],

            'tc_remark_option_id' =>
                $validated[
                    'tc_remark_option_id'
                ] ?? null,

            'tc_last_result_option_id' =>
                $validated[
                    'tc_last_result_option_id'
                ] ?? null,

            'conduct' =>
                $validated[
                    'conduct'
                ] ?? null,

            'next_school' =>
                $validated[
                    'next_school'
                ] ?? null,

            'next_class' =>
                $validated[
                    'next_class'
                ] ?? null,

            'remarks' =>
                $validated[
                    'remarks'
                ] ?? null,


            /*
            |--------------------------------------------------------------------------
            | Clearance
            |--------------------------------------------------------------------------
            */

            'fees_cleared' =>
                $request->boolean(
                    'fees_cleared'
                ),

            'library_cleared' =>
                $request->boolean(
                    'library_cleared'
                ),

            'transport_cleared' =>
                $request->boolean(
                    'transport_cleared'
                ),


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' =>
                'draft',

            'created_by' =>
                auth()->id(),

        ]);


        return redirect()
            ->route(
                'student-management.manual-transfer-certificate'
            )
            ->with(
                'success',
                'Manual Transfer Certificate '
                .
                $certificate->tc_number
                .
                ' created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Issue Manual TC
    |--------------------------------------------------------------------------
    */

    public function issue(
        TransferCertificate $certificate
    ) {

        $schoolId = auth()->user()->school_id;


        if (
            (int) $certificate->school_id
            !==
            (int) $schoolId
            ||
            !$certificate->is_manual
        ) {
            abort(403);
        }


        if ($certificate->status !== 'draft') {

            return back()->with(
                'error',
                'Only Draft Transfer Certificates can be issued.'
            );
        }


        DB::transaction(
            function () use (
                $certificate,
                $schoolId
            ) {

                $certificate =
                    TransferCertificate::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'is_manual',
                        true
                    )
                    ->whereKey(
                        $certificate->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                if (
                    $certificate->status
                    !==
                    'draft'
                ) {
                    return;
                }


                /*
                 * Manual TC does NOT alter
                 * student_enrollments.
                 */

                $certificate->update([

                    'status' =>
                        'issued',

                    'issued_by' =>
                        auth()->id(),

                    'issued_at' =>
                        now(),

                ]);

            }
        );


        return back()->with(
            'success',
            'Manual Transfer Certificate issued successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        TransferCertificate $certificate
    ) {

        $schoolId = auth()->user()->school_id;


        if (
            (int) $certificate->school_id
            !==
            (int) $schoolId
            ||
            !$certificate->is_manual
        ) {
            abort(403);
        }


        if ($certificate->status !== 'draft') {

            return back()->with(
                'error',
                'Only Draft Transfer Certificates can be cancelled.'
            );
        }


        $validated =
            $request->validate([

                'cancellation_reason' => [
                    'required',
                    'string',
                    'max:1000',
                ],

            ]);


        $certificate->update([

            'status' =>
                'cancelled',

            'cancelled_by' =>
                auth()->id(),

            'cancelled_at' =>
                now(),

            'cancellation_reason' =>
                $validated[
                    'cancellation_reason'
                ],

        ]);


        return back()->with(
            'success',
            'Manual Transfer Certificate cancelled successfully.'
        );
    }
}