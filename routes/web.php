<?php

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
