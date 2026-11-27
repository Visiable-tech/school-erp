<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Teacher\{ClassworkController as C,NoticeController as N,TeacherMobileAccess as A};
Route::prefix('v1/teacher')->middleware(['auth:sanctum',A::class])->group(function(){
 Route::get('classwork/assignments',[C::class,'assignments']);
 Route::get('classwork',[C::class,'index']);
 Route::post('classwork',[C::class,'store']);
 Route::get('classwork/{id}',[C::class,'show'])->whereNumber('id');
 Route::put('classwork/{id}',[C::class,'update'])->whereNumber('id');
 Route::prefix('notices')->middleware(A::class.':teacher-notice.view')->group(function(){
 Route::get('options',[N::class,'options'])->middleware(A::class.':teacher-notice.create');
 Route::get('recipients',[N::class,'recipients'])->middleware(A::class.':teacher-notice.create');
 Route::get('/',[N::class,'index']);
 Route::post('/',[N::class,'store'])->middleware(A::class.':teacher-notice.create');
 Route::get('{id}',[N::class,'show'])->whereNumber('id');
 Route::post('{id}/publish',[N::class,'publish'])->whereNumber('id')->middleware(A::class.':teacher-notice.publish');
 Route::post('{id}/withdraw',[N::class,'withdraw'])->whereNumber('id')->middleware(A::class.':teacher-notice.publish');
 Route::get('{id}/attachments/{file}',[N::class,'download'])->whereNumber(['id','file'])->name('api.teacher.notices.download');
 });
});
