<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontOfficeController;
Route::middleware('auth')->prefix('front-office')->name('front-office.')->group(function(){
 Route::get('/',[FrontOfficeController::class,'index'])->name('index');
 Route::get('/create',[FrontOfficeController::class,'create'])->name('create');
 Route::post('/',[FrontOfficeController::class,'store'])->name('store');
 Route::get('/report',[FrontOfficeController::class,'report'])->name('report');
 Route::get('/{id}/edit',[FrontOfficeController::class,'edit'])->whereNumber('id')->name('edit');
 Route::put('/{id}',[FrontOfficeController::class,'update'])->whereNumber('id')->name('update');
 Route::post('/{id}/checkout',[FrontOfficeController::class,'checkout'])->whereNumber('id')->name('checkout');
});
