<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Si la petición no es a una ruta de la API, devolver JSON
if (!str_starts_with($_SERVER['REQUEST_URI'], '/api')) {
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(['message' => 'Solo disponible como API']);
    exit;
}

$app->handleRequest(Request::capture());
