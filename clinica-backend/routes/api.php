<?php

use App\Http\Controllers\CitaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistorialController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas con Sanctum
Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('usuarios', [UserController::class, 'index']);
    Route::put('usuarios/{id}/rol', [UserController::class, 'updateRole']);
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);

    });

    Route::middleware(['auth:sanctum', 'role:Administrador,Especialista'])->group(function () {
    // rutas para admin o especialista
    });

    // Perfil usuario autenticado
    Route::get('perfil', [UserController::class, 'me']);

    // Gestión usuarios
    Route::put('usuarios', UserController::class)->except(['update']);
    Route::put('usuarios/{id}/rol', [UserController::class, 'updateRole']);
    Route::delete('usuarios/{id}', [UserController::class, 'destroy']);

    // Gestión citas
    Route::apiResource('citas', CitaController::class);
    Route::put('citas/{id}/cancelar', [CitaController::class, 'cancelar']);

    // Gestión historiales médicos
    Route::apiResource('historiales', HistorialController::class);

    // Documentos relacionados con historiales
    Route::get('historiales/{historial_id}/documentos', [DocumentoController::class, 'index']);
    Route::post('historiales/{historial_id}/documentos', [DocumentoController::class, 'store']);

    // Documentos individuales
    Route::get('documentos/{id}', [DocumentoController::class, 'show']);
    Route::get('documentos/{id}/descargar', [DocumentoController::class, 'descargar']);
    Route::delete('documentos/{id}', [DocumentoController::class, 'destroy']);

    // Logout
    Route::post('logout', [AuthController::class, 'logout']);
});
