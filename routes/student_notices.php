<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherStudentNoticeController as C;
Route::middleware(['auth','role:Teacher|Admin|Super Admin'])->prefix('teacher/student-notices')->name('teacher.student-notices.')->group(function(){
 Route::get('/',[C::class,'index'])->name('index');
 Route::get('/create',[C::class,'create'])->name('create');
 Route::get('/recipients',[C::class,'recipients'])->name('recipients');
 Route::post('/',[C::class,'store'])->name('store');
 Route::get('/{id}',[C::class,'show'])->whereNumber('id')->name('show');
 Route::post('/{id}/publish',[C::class,'publish'])->name('publish');
 Route::post('/{id}/withdraw',[C::class,'withdraw'])->name('withdraw');
 Route::get('/{id}/attachment/{file}',[C::class,'download'])->name('download');
});
