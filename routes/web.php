<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/', [ChatController::class, 'index']);
Route::post('/enviar', [ChatController::class, 'enviar'])->name('enviar');
Route::post('/salvar-atendimento', [ChatController::class, 'salvarAtendimento']);

