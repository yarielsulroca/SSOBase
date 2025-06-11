<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LdapAuthController;

// Rutas de autenticación LDAP
Route::prefix('v1')->group(function () {
    Route::post('/login', [LdapAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/check', [LdapAuthController::class, 'check']);
    Route::post('/logout', [LdapAuthController::class, 'logout']);
});

// Ruta de prueba
Route::get('/test', function () {
    return response()->json(['message' => 'API funcionando correctamente']);
});

