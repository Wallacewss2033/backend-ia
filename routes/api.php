<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    
    // Users
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store']);
    Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show']);
    Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);

    // Chats
    Route::get('/chats', [\App\Http\Controllers\ChatController::class, 'index']);
    Route::post('/chats', [\App\Http\Controllers\ChatController::class, 'store']);
    Route::get('/chats/{id}', [\App\Http\Controllers\ChatController::class, 'show']);
    Route::delete('/chats/{id}', [\App\Http\Controllers\ChatController::class, 'destroy']);

    // Documents
    Route::get('/documents', [\App\Http\Controllers\DocumentController::class, 'index']);
    Route::post('/documents', [\App\Http\Controllers\DocumentController::class, 'store']);
    Route::delete('/documents/{id}', [\App\Http\Controllers\DocumentController::class, 'destroy']);

    // Messages
    Route::post('/messages/send', [\App\Http\Controllers\MessageController::class, 'sendMessage']);
    
    // Appointments
    Route::get('/appointments', [\App\Http\Controllers\AppointmentController::class, 'index']);
    Route::post('/appointments', [\App\Http\Controllers\AppointmentController::class, 'store']);
    Route::put('/appointments/{id}', [\App\Http\Controllers\AppointmentController::class, 'update']);
    Route::delete('/appointments/{id}', [\App\Http\Controllers\AppointmentController::class, 'destroy']);

    // Website Management - Categories
    Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index']);
    Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store']);
    Route::get('/categories/{id}', [\App\Http\Controllers\CategoryController::class, 'show']);
    Route::put('/categories/{id}', [\App\Http\Controllers\CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [\App\Http\Controllers\CategoryController::class, 'destroy']);

    // Website Management - Authors
    Route::get('/authors', [\App\Http\Controllers\AuthorController::class, 'index']);
    Route::post('/authors', [\App\Http\Controllers\AuthorController::class, 'store']);
    Route::get('/authors/{id}', [\App\Http\Controllers\AuthorController::class, 'show']);
    Route::put('/authors/{id}', [\App\Http\Controllers\AuthorController::class, 'update']);
    Route::delete('/authors/{id}', [\App\Http\Controllers\AuthorController::class, 'destroy']);

    // Website Management - Articles
    Route::get('/articles', [\App\Http\Controllers\ArticleController::class, 'index']);
    Route::post('/articles', [\App\Http\Controllers\ArticleController::class, 'store']);
    Route::get('/articles/{id}', [\App\Http\Controllers\ArticleController::class, 'show']);
    Route::put('/articles/{id}', [\App\Http\Controllers\ArticleController::class, 'update']);
    Route::delete('/articles/{id}', [\App\Http\Controllers\ArticleController::class, 'destroy']);

    // Website Management - SiteDomains
    Route::get('/sites-domains', [\App\Http\Controllers\SiteDomainController::class, 'index']);
    Route::post('/sites-domains', [\App\Http\Controllers\SiteDomainController::class, 'store']);
    Route::get('/sites-domains/{id}', [\App\Http\Controllers\SiteDomainController::class, 'show']);
    Route::put('/sites-domains/{id}', [\App\Http\Controllers\SiteDomainController::class, 'update']);
    Route::post('/sites-domains/upload-image', [\App\Http\Controllers\SiteDomainController::class, 'uploadImage']);
    Route::delete('/sites-domains/{id}', [\App\Http\Controllers\SiteDomainController::class, 'destroy']);
    Route::post('/sites-domains/{id}/verify-dns', [\App\Http\Controllers\SiteDomainController::class, 'verifyDns']);

    // SeoMetas
    Route::get('/seo-metas', [\App\Http\Controllers\SeoMetaController::class, 'index']);
    Route::post('/seo-metas', [\App\Http\Controllers\SeoMetaController::class, 'store']);
    Route::get('/seo-metas/{id}', [\App\Http\Controllers\SeoMetaController::class, 'show']);
    Route::put('/seo-metas/{id}', [\App\Http\Controllers\SeoMetaController::class, 'update']);
    Route::delete('/seo-metas/{id}', [\App\Http\Controllers\SeoMetaController::class, 'destroy']);
});
