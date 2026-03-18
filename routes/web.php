<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PrediccionesController;
use App\Http\Controllers\ReporteController;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rutas para recursos
Route::resources([
    'categorias' => CategoriaController::class,
    'insumos' => InsumoController::class,
    'unidades' => UnidadMedidaController::class,
    'proveedores' => ProveedorController::class,
    'movimientos' => MovimientoInventarioController::class,
    'recetas' => RecetaController::class,
    'ventas' => VentaController::class,
]);

// Rutas para predicciones y reportes
//Route::get('predicciones', [PrediccionesController::class, 'index'])->name('predicciones.index');
Route::get('predicciones', [PrediccionesController::class, 'index'])
    ->name('predicciones.index')
    ->middleware('role:admin'); // Solo usuarios con rol "admin"

Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

//fernando
Route::get('/reportes/vista-multiple', [ReporteController::class, 'vistaMultiple'])->name('reportes.vista-multiple');
//fin fernando
Route::get('/reportes/ventas-data', [ReporteController::class, 'getVentasData'])->name('reportes.ventas-data');
Route::get('/reportes/parcial/semanal', [ReporteController::class, 'parcialSemanal']);
Route::get('/reportes/parcial/mensual', [ReporteController::class, 'parcialMensual']);
Route::get('/reportes/parcial/anual', [ReporteController::class, 'parcialAnual']);

// Rutas de perfil
Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

// Rutas de usuarios (admin)
//Route::resource('users', UserController::class);

Route::resource('users', UserController::class)->middleware('role:admin'); // Solo usuarios con rol "admin"
/*
Route::get('users', [UserController::class, 'index'])->name('users.index')
    ->middleware('role:admin'); // Solo usuarios con rol "admin"

Route::get('users/create', [UserController::class, 'create'])->name('users.create');
*/


