<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherStudentNoticeController as C;
Route::middleware(['auth','role:Teacher|Admin|Super Admin','permission:teacher-notice.view'])->prefix('teacher/student-notices')->name('teacher.student-notices.')->group(function(){
 Route::get('/',[C::class,'index'])->name('index');
 Route::get('/create',[C::class,'create'])->middleware('permission:teacher-notice.create')->name('create');
 Route::get('/recipients',[C::class,'recipients'])->middleware('permission:teacher-notice.create')->name('recipients');
 Route::post('/',[C::class,'store'])->middleware('permission:teacher-notice.create')->name('store');
 Route::get('/{id}',[C::class,'show'])->whereNumber('id')->name('show');
 Route::post('/{id}/publish',[C::class,'publish'])->middleware('permission:teacher-notice.publish')->name('publish');
 Route::post('/{id}/withdraw',[C::class,'withdraw'])->middleware('permission:teacher-notice.publish')->name('withdraw');
 Route::get('/{id}/attachment/{file}',[C::class,'download'])->name('download');
});
