<?php
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth','role:Super Admin'])->prefix('access')->name('access.')->group(function () {
    Route::get('roles', [RoleManagementController::class,'index'])->name('roles.index');
    Route::get('roles/create', [RoleManagementController::class,'create'])->name('roles.create');
    Route::post('roles', [RoleManagementController::class,'store'])->name('roles.store');
    Route::get('roles/{role}/edit', [RoleManagementController::class,'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleManagementController::class,'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleManagementController::class,'destroy'])->name('roles.destroy');

});

Route::middleware(['auth','role:Super Admin|Admin'])->prefix('access')->name('access.')->group(function () {
    Route::get('users', [UserManagementController::class,'index'])->name('users.index');
    Route::get('users/create', [UserManagementController::class,'create'])->name('users.create');
    Route::post('users', [UserManagementController::class,'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserManagementController::class,'edit'])->name('users.edit');
    Route::put('users/{user}', [UserManagementController::class,'update'])->name('users.update');
});
