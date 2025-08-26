<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\MissionRequestController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\WorkflowController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\UploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    // Leave Request routes
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
    Route::get('/leave-requests/{id}', [LeaveRequestController::class, 'show']);
    Route::put('/leave-requests/{id}', [LeaveRequestController::class, 'update']);
    Route::delete('/leave-requests/{id}', [LeaveRequestController::class, 'destroy']);

    // Mission Request routes
    Route::get('/mission-requests', [MissionRequestController::class, 'index']);
    Route::post('/mission-requests', [MissionRequestController::class, 'store']);
    Route::get('/mission-requests/{id}', [MissionRequestController::class, 'show']);
    Route::put('/mission-requests/{id}', [MissionRequestController::class, 'update']);
    Route::delete('/mission-requests/{id}', [MissionRequestController::class, 'destroy']);

    // Approval routes
    Route::get('/approvals/pending', [ApprovalController::class, 'pending']);
    Route::post('/approvals/approve', [ApprovalController::class, 'approve']);
    Route::post('/approvals/reject', [ApprovalController::class, 'reject']);
    Route::get('/requests/{type}/{id}', [ApprovalController::class, 'show']);

    // User Management routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/users/assign-role', [UserController::class, 'assignRole']);
        Route::get('/roles', [UserController::class, 'getRoles']);
    });

    // Department Management routes
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

    // Workflow Management routes
    Route::get('/workflows', [WorkflowController::class, 'index']);
    Route::post('/workflows', [WorkflowController::class, 'store']);
    Route::put('/workflows/{id}', [WorkflowController::class, 'update']);

    // Dashboard & Analytics routes
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/analytics', [DashboardController::class, 'analytics']);

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // File Upload routes
    Route::post('/upload', [UploadController::class, 'upload']);

    // Search routes
    Route::get('/search/requests', [SearchController::class, 'searchRequests']);
    Route::get('/requests/history', [SearchController::class, 'requestHistory']);
});
