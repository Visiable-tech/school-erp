<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransportOperationsController as T;
Route::middleware(['web','auth','role:Admin|Super Admin'])->prefix('transport')->name('transport.')->group(function(){
 Route::get('/dashboard',[T::class,'dashboard'])->name('dashboard');
 Route::get('/register/{type}',[T::class,'index'])->name('register');
 Route::post('/register/{type}',[T::class,'store'])->name('register.store');
 Route::put('/register/{type}/{id}',[T::class,'update'])->whereNumber('id')->name('register.update');
});
