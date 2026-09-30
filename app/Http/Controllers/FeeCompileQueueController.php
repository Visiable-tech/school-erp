<?php

namespace App\Http\Controllers;

use App\Models\FeeCompileQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeCompileQueueController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Queue List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId =
            Auth::user()->school_id;

        $query = FeeCompileQueue::with([
            'academicYear',
            'schoolClass',
            'section',
            'feeStructure',
            'createdBy',
        ])
        ->where(
            'school_id',
            $schoolId
        );


        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        if (
            $request->filled(
                'academic_year_id'
            )
        ) {

            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }


        $queues = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'fee-compile-queues.index',
            compact('queues')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | View Queue
    |--------------------------------------------------------------------------
    */

    public function show(
        FeeCompileQueue $feeCompileQueue
    ) {
        $this->authorizeSchool(
            $feeCompileQueue
        );

        $feeCompileQueue->load([
            'academicYear',
            'schoolClass',
            'section',
            'feeStructure',
            'createdBy',
        ]);

        return view(
            'fee-compile-queues.show',
            compact('feeCompileQueue')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Pending Queue
    |--------------------------------------------------------------------------
    */

    public function cancel(
        FeeCompileQueue $feeCompileQueue
    ) {
        $this->authorizeSchool(
            $feeCompileQueue
        );


        if (
            $feeCompileQueue->status !==
            'pending'
        ) {

            return back()->with(
                'error',
                'Only pending compile jobs can be cancelled.'
            );
        }


        $feeCompileQueue->update([
            'status' => 'cancelled',
            'completed_at' => now(),
        ]);


        return back()->with(
            'success',
            'Fee compile job cancelled successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Retry Failed Queue
    |--------------------------------------------------------------------------
    */

    public function retry(
        FeeCompileQueue $feeCompileQueue
    ) {
        $this->authorizeSchool(
            $feeCompileQueue
        );


        if (
            $feeCompileQueue->status !==
            'failed'
        ) {

            return back()->with(
                'error',
                'Only failed compile jobs can be retried.'
            );
        }


        $feeCompileQueue->update([

            'status' =>
                'pending',

            'processed_students' =>
                0,

            'compiled_students' =>
                0,

            'skipped_students' =>
                0,

            'failed_students' =>
                0,

            'progress' =>
                0,

            'error_details' =>
                null,

            'error_message' =>
                null,

            'started_at' =>
                null,

            'completed_at' =>
                null,
        ]);


        /*
         * Later this is where we'll dispatch:
         *
         * CompileStudentFeesJob::dispatch(
         *     $feeCompileQueue->id
         * );
         */


        return back()->with(
            'success',
            'Compile job moved back to pending queue.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */

    private function authorizeSchool(
        FeeCompileQueue $queue
    ): void {

        abort_unless(
            (int) $queue->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }
}