<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ValidadorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas abertas para a ferramenta OPECS externa
Route::get('/paineis/configuracoes', [ValidadorController::class, 'listarConfiguracoes']);
Route::post('/webhook/media-receiver', [ValidadorController::class, 'receberWebhook']);