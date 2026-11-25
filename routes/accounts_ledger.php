<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountLedgerController;
Route::middleware(['auth','permission:accounts.view','permission:accounts.ledger.view'])->prefix('accounts/ledger')->name('accounts.ledger.')->group(function(){
Route::get('/',[AccountLedgerController::class,'index'])->name('index');
Route::post('/accounts',[AccountLedgerController::class,'account'])->middleware('permission:accounts.ledger.create')->name('account');
Route::post('/entries',[AccountLedgerController::class,'store'])->middleware('permission:accounts.ledger.create')->name('store');
Route::post('/entries/{id}/review',[AccountLedgerController::class,'review'])->middleware('permission:accounts.ledger.approve')->name('review');
Route::get('/book/{id}',[AccountLedgerController::class,'book'])->name('book');
});
