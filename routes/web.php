<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Este archivo está vacío intencionalmente ya que la aplicación funciona
| exclusivamente como microservicio API. Todas las rutas están definidas
| en routes/api.php
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', [DocumentationController::class, 'show'])->name('documentation');
