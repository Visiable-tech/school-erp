<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Teacher\PortalController as T;
use App\Http\Controllers\Api\Teacher\StudentAttendanceController;


Route::prefix('v1/teacher')->group(function(){
    Route::post('login',[T::class,'login'])->middleware('throttle:10,1');
    Route::middleware('auth:sanctum')->group(function(){
        foreach(['profile'=>'profile','dashboard'=>'dashboard','assignments'=>'classes','assignments/{assignment}/students'=>'roster','payslips'=>'payslips','feeds/{feed}'=>'feed'] as $path=>$method) Route::get($path,[T::class,$method]);
        Route::post('logout',[T::class,'logout']); Route::post('change-password',[T::class,'password']); Route::post('messages',[T::class,'send']);
        Route::get('assignments/{assignment}/attendance',[T::class,'attendance']); Route::post('assignments/{assignment}/attendance',[T::class,'saveAttendance']);
        Route::match(['get','post'],'assignments/{assignment}/period-attendance',[T::class,'period']); Route::match(['get','post'],'assessments/{id}/marks',[T::class,'marks']);
        Route::match(['get','post'],'records/{resource}',[T::class,'resource']); Route::match(['get','put','delete'],'records/{resource}/{id}',[T::class,'resource'])->whereNumber('id');
    });
});


Route::prefix('v1/teacher/student-attendance')->middleware('auth:sanctum')->group(function(){
    Route::get('classes',[StudentAttendanceController::class,'classes']);
    Route::get('sections',[StudentAttendanceController::class,'sections']);
    Route::get('students',[StudentAttendanceController::class,'students']);
    Route::post('submit',[StudentAttendanceController::class,'store']);
});
