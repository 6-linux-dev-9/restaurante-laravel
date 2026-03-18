<?php

use App\Http\Controllers\RestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Rutas API de ejemplo
//Route::get('/ventas/agrupadas-productos', [RestController::class, 'obtenerVentasAgrupadasPorRecetaNombre']);
Route::post('/ventas/agrupadas', [RestController::class, 'obtenerVentasAgrupadas']);



