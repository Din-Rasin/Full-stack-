<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('workflows', App\Http\Controllers\WorkflowController::class);
    Route::resource('users', App\Http\Controllers\AdminController::class);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Request routes
    Route::resource('requests', App\Http\Controllers\RequestController::class);
    Route::resource('leave-requests', App\Http\Controllers\LeaveRequestController::class);
    Route::resource('mission-requests', App\Http\Controllers\MissionRequestController::class);

    // Notification routes
    Route::resource('notifications', App\Http\Controllers\NotificationController::class);

    // Audit log routes
    Route::resource('audit-logs', App\Http\Controllers\AuditLogController::class);
});

require __DIR__.'/auth.php';
