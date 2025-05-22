<?php

use App\Http\Controllers\CitaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistorialController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('citas', CitaController::class);

    Route::get('documentos', [DocumentoController::class, 'index']);
    Route::post('documentos', [DocumentoController::class, 'store']);
    Route::get('documentos/{id}', [DocumentoController::class, 'show']);
    Route::delete('documentos/{id}', [DocumentoController::class, 'destroy']);

    Route::get('/citas', [CitaController::class, 'index']);
    Route::post('/citas', [CitaController::class, 'store']);
    Route::get('/citas/{id}', [CitaController::class, 'show']);
    Route::put('/citas/{id}/cancelar', [CitaController::class, 'cancelar']);
    Route::delete('/citas/{id}', [CitaController::class, 'destroy']);

    Route::get('/usuarios', [UserController::class, 'index']); 
    Route::get('/usuarios/{id}', [UserController::class, 'show']);
    Route::get('/perfil', [UserController::class, 'me']);
    Route::put('/usuarios/{id}/rol', [UserController::class, 'updateRole']);
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);

    Route::get('/historiales', [HistorialController::class, 'index']);
    Route::get('/historiales/{id}', [HistorialController::class, 'show']);
    Route::post('/historiales', [HistorialController::class, 'store']);
    Route::put('/historiales/{id}', [HistorialController::class, 'update']);
    Route::delete('/historiales/{id}', [HistorialController::class, 'destroy']);

    Route::get('/historiales/{historial_id}/documentos', [DocumentoController::class, 'index']);
    Route::post('/historiales/{historial_id}/documentos', [DocumentoController::class, 'store']);
    Route::get('/documentos/{id}/descargar', [DocumentoController::class, 'descargar']);
    Route::delete('/documentos/{id}', [DocumentoController::class, 'destroy']);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('perfil', [AuthController::class, 'perfil']);
});