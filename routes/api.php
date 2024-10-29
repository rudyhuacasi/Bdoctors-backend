<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MedicalProfileController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Definición de las rutas para el recurso 'medical'
    Route::resource('medical', MedicalProfileController::class)->only([
        'store',
        'update',
        'destroy'
    ]);
    Route::get('/user-profiles/{slug}/{id}', [MedicalProfileController::class, 'showProfile']);
    Route::get('/user-profiles', [MedicalProfileController::class, 'profilo']); // Obtener perfiles del usuario autenticado
});

Route::get('/medical', [MedicalProfileController::class, 'index']); // Listar perfiles médicos
Route::get('/medical/{slug}', [MedicalProfileController::class, 'show']);

Route::post('/message/{id}', [MessageController::class, 'store']);



