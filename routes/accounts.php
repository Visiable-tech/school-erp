<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountsController;
Route::middleware(['auth','permission:accounts.view'])->prefix('accounts')->name('accounts.')->group(function(){
 Route::get('/',[AccountsController::class,'index'])->name('index');
 Route::get('/expenses',[AccountsController::class,'expenses'])->name('expenses');
 Route::get('/categories',[AccountsController::class,'categories'])->middleware('permission:accounts.category.manage')->name('categories');
 Route::post('/categories',[AccountsController::class,'categoryStore'])->middleware('permission:accounts.category.manage')->name('categories.store');
 Route::post('/expenses',[AccountsController::class,'store'])->middleware('permission:accounts.expense.create')->name('store');
 Route::post('/expenses/{id}/review',[AccountsController::class,'review'])->middleware('permission:accounts.expense.approve')->name('review');
 Route::post('/expenses/{id}/pay',[AccountsController::class,'pay'])->middleware('permission:accounts.expense.pay')->name('pay');
 Route::get('/expenses/{id}/voucher',[AccountsController::class,'voucher'])->name('voucher');
});
