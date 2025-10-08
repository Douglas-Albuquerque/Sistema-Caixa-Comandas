<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComandaController;

Route::apiResource('comandas', ComandaController::class);