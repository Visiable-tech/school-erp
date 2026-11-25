<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountVendorController;
Route::middleware(['auth','permission:accounts.view'])->prefix('accounts/vendors')->name('accounts.vendors.')->group(function(){
 Route::get('/',[AccountVendorController::class,'index'])->name('index');
 Route::post('/',[AccountVendorController::class,'store'])->middleware('permission:accounts.vendor.manage')->name('store');
 Route::put('/{id}',[AccountVendorController::class,'update'])->middleware('permission:accounts.vendor.manage')->name('update');
 Route::get('/{id}/ledger',[AccountVendorController::class,'ledger'])->name('ledger');
 Route::post('/{id}/invoices',[AccountVendorController::class,'invoice'])->middleware('permission:accounts.vendor.invoice')->name('invoice');
 Route::post('/invoices/{id}/review',[AccountVendorController::class,'review'])->middleware('permission:accounts.vendor.approve')->name('review');
 Route::post('/invoices/{id}/pay',[AccountVendorController::class,'pay'])->middleware('permission:accounts.vendor.pay')->name('pay');
 Route::get('/payments/{id}/voucher',[AccountVendorController::class,'voucher'])->name('voucher');
});
