<?php

use App\Http\Controllers\AjustesController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ResguardoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/movimientos');

Route::patch('movimientos/{movimiento}/estado', [MovimientoController::class, 'cambiarEstado'])
    ->whereNumber('movimiento')
    ->name('movimientos.estado');

Route::resource('movimientos', MovimientoController::class)
    ->whereNumber('movimiento');

Route::resource('resguardos', ResguardoController::class)
    ->whereNumber('resguardo');

Route::controller(AjustesController::class)->group(function () {
    Route::get('ajustes', 'index')->name('ajustes.index');
    Route::post('ajustes/foto', 'actualizarFoto')->name('ajustes.foto');
    Route::delete('ajustes/foto', 'eliminarFoto')->name('ajustes.foto.eliminar');
    Route::put('ajustes/contrasena', 'actualizarContrasena')->name('ajustes.contrasena');
    Route::post('cerrar-sesion', 'cerrarSesion')->name('cerrar-sesion');
    Route::get('sesion-cerrada', 'sesionCerrada')->name('sesion.cerrada');
});
