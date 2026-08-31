<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
    Route::apiResource('chats', \App\Http\Controllers\ChatController::class)->except(['update']);
    Route::apiResource('documents', \App\Http\Controllers\DocumentController::class)->only(['index', 'store', 'destroy']);
    Route::post('/messages/send', [\App\Http\Controllers\MessageController::class, 'sendMessage']);
});
