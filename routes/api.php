<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MovementOrderController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReasonController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile/delete-avatar', [ProfileController::class, 'deleteAvatar']);

    // Admin Only Routes
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('partners', PartnerController::class);
    Route::apiResource('reasons', ReasonController::class);

    // Product Management (Full CRUD)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/top-products', [ReportController::class, 'topProducts']);
        Route::get('/top-categories', [ReportController::class, 'topCategories']);
        Route::get('/revenue', [ReportController::class, 'revenue']);
        Route::get('/per-supplier', [ReportController::class, 'perSupplier']);
    });

    // Shared Routes (Admin & Seller)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::apiResource('movement-orders', MovementOrderController::class)->only(['index', 'store', 'show']);
});
