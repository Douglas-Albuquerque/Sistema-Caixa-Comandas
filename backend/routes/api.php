<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComandaController;

Route::get('/mesas-disponiveis', [ComandaController::class, 'mesasDisponiveis']);
Route::get('/mesas', [ComandaController::class, 'todasMesas']);
Route::post('/pedido-cozinha', [ComandaController::class, 'pedidoCozinha']);
Route::apiResource('comandas', ComandaController::class);