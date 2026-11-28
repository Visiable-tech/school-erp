<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\AdmissionApplication;
Route::middleware(['auth','permission:admission-application.edit'])->post('/admission/applications/{application}/waitlist',function(Request $request, AdmissionApplication $application){
 abort_unless($request->user()->school_id && (int)$request->user()->school_id === (int)$application->school_id,403);
 $updated=AdmissionApplication::whereKey($application->id)->whereIn('application_status',['submitted','under_review'])->update(['application_status'=>'waiting_list']);
 return back()->with($updated?'success':'error',$updated?'Application moved to waiting list.':'Application cannot be waitlisted.');
})->name('admission.waitlist');
