<?php

use App\Http\Controllers\Api\V1\AnimalsController;
use App\Http\Controllers\Api\V1\ArtificialInseminationsController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BreedsController;
use App\Http\Controllers\Api\V1\HerdsController;
use App\Http\Controllers\Api\V1\PregnanciesController;
use App\Http\Controllers\Api\V1\PregnancyRecordsController;
use App\Http\Controllers\Api\V1\ReproductionRecordsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Mobile / external clients should use these endpoints.
|
| Conventions:
| - All endpoints are versioned under /api/v1
| - Auth uses Laravel Sanctum personal access tokens (Bearer tokens)
| - Responses are JSON
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
        ]);
    })->name('api.v1.health');

    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.v1.auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');

        Route::apiResource('animals', AnimalsController::class);
        Route::apiResource('breeds', BreedsController::class);
        Route::apiResource('pregnancies', PregnanciesController::class);
        Route::apiResource('pregnancy-records', PregnancyRecordsController::class)->parameters([
            'pregnancy-records' => 'pregnancy_record',
        ]);
        Route::apiResource('herds', HerdsController::class);
        Route::apiResource('reproduction-records', ReproductionRecordsController::class)->parameters([
            'reproduction-records' => 'reproduction_record',
        ]);

        Route::apiResource('artificial-inseminations', ArtificialInseminationsController::class)->parameters([
            'artificial-inseminations' => 'artificial_insemination',
        ]);
    });
});
