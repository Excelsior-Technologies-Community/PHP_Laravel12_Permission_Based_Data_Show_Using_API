<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleWiseDataController;
use App\Http\Controllers\Api\Admin\RolePermissionController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/role-wise-data', [RoleWiseDataController::class, 'index']);

    // Admin side
    Route::get('/admin/roles', [RolePermissionController::class, 'roles']);
    Route::post('/admin/assign-permission', [RolePermissionController::class, 'assignPermission']);
});


Route::get('/check', function () {
    return response()->json(['status' => 'API WORKING']);
});
