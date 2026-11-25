<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountReportController;
Route::get('accounts/reports',[AccountReportController::class,'index'])->middleware(['auth','permission:accounts.view','permission:accounts.reports.view'])->name('accounts.reports.index');
