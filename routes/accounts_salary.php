<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountSalaryController;
Route::middleware(['auth','permission:accounts.view','permission:accounts.salary.view'])->prefix('accounts/salary')->name('accounts.salary.')->group(function(){Route::get('/',[AccountSalaryController::class,'index'])->name('index');Route::get('/export',[AccountSalaryController::class,'export'])->name('export');Route::get('/voucher/{id}',[AccountSalaryController::class,'voucher'])->name('voucher');Route::post('/{id}/pay',[AccountSalaryController::class,'pay'])->middleware('permission:accounts.salary.pay')->name('pay');});
