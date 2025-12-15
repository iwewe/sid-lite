<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\WargaController;
use App\Http\Controllers\API\ModuleController;
use App\Http\Controllers\API\ResponseController;

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

// Public routes (no authentication required for development)
Route::prefix('v1')->group(function () {

    // Warga routes
    Route::prefix('warga')->group(function () {
        Route::get('/search', [WargaController::class, 'search']); // GET /api/v1/warga/search?q=...
        Route::get('/{nik}', [WargaController::class, 'show']); // GET /api/v1/warga/3173...
        Route::post('/', [WargaController::class, 'store']); // POST /api/v1/warga
        Route::put('/{nik}', [WargaController::class, 'update']); // PUT /api/v1/warga/3173...
        Route::delete('/{nik}', [WargaController::class, 'destroy']); // DELETE /api/v1/warga/3173...

        // Warga responses
        Route::get('/{nik}/responses', [ResponseController::class, 'getWargaResponses']); // GET /api/v1/warga/3173.../responses
        Route::get('/{nik}/responses/{moduleCode}', [ResponseController::class, 'getModuleResponse']); // GET /api/v1/warga/3173.../responses/jamban
    });

    // Module routes
    Route::prefix('modules')->group(function () {
        Route::get('/', [ModuleController::class, 'index']); // GET /api/v1/modules
        Route::get('/{code}', [ModuleController::class, 'show']); // GET /api/v1/modules/jamban
        Route::post('/', [ModuleController::class, 'store']); // POST /api/v1/modules (admin only)
        Route::put('/{code}', [ModuleController::class, 'update']); // PUT /api/v1/modules/jamban
        Route::delete('/{code}', [ModuleController::class, 'destroy']); // DELETE /api/v1/modules/jamban
    });

    // Response routes
    Route::prefix('responses')->group(function () {
        Route::post('/', [ResponseController::class, 'saveResponse']); // POST /api/v1/responses
        Route::delete('/{id}', [ResponseController::class, 'deleteResponse']); // DELETE /api/v1/responses/1
    });

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [ResponseController::class, 'getDashboardStats']); // GET /api/v1/dashboard/stats
    });
});

// Protected routes (require authentication)
// Uncomment when Sanctum is configured
/*
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Protected endpoints here
});
*/
