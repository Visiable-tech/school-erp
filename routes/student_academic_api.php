<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentAcademicController;
Route::prefix('student')->middleware('auth:sanctum')->group(function () {
    Route::get('attendance', [StudentAcademicController::class, 'attendance']);
    Route::get('classwork', [StudentAcademicController::class, 'classwork']);
    Route::get('homework', [StudentAcademicController::class, 'homework']);
    Route::get('calendar', [StudentAcademicController::class, 'calendar']);
});
