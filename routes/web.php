<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PublicSiteController;

Route::middleware(['tenant'])->group(function () {
    Route::get('/', [PublicSiteController::class, 'index'])->name('site.index');
    Route::get('/categoria/{slug}', [PublicSiteController::class, 'showCategory'])->name('site.category.show');
    Route::get('/autores', [PublicSiteController::class, 'indexAuthors'])->name('site.author.index');
    Route::get('/autor/{slug}', [PublicSiteController::class, 'showAuthor'])->name('site.author.show');
    // Rota opcional para exibir artigo
    Route::get('/{slug}', [PublicSiteController::class, 'showArticle'])->name('site.article.show');
});
