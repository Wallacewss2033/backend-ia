<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PublicSiteController;

Route::middleware(['tenant'])->group(function () {
    Route::get('/', [PublicSiteController::class, 'index'])->name('site.index');
    Route::get('/categoria/{slug}', [PublicSiteController::class, 'showCategory'])->name('site.category.show');
    Route::get('/autores', [PublicSiteController::class, 'indexAuthors'])->name('site.author.index');
    Route::get('/autor/{slug}', [PublicSiteController::class, 'showAuthor'])->name('site.author.show');

    // Páginas Institucionais (Essenciais para AdSense)
    Route::get('/politica-de-privacidade', [PublicSiteController::class, 'privacyPolicy'])->name('site.privacy');
    Route::get('/termos-de-uso', [PublicSiteController::class, 'termsOfUse'])->name('site.terms');
    Route::get('/sobre-nos', [PublicSiteController::class, 'aboutUs'])->name('site.about');
    Route::get('/contato', [PublicSiteController::class, 'contact'])->name('site.contact');
    Route::get('/ads.txt', [PublicSiteController::class, 'adsTxt'])->name('site.ads.txt');
    // Rota opcional para exibir artigo
    Route::get('/{slug}', [PublicSiteController::class, 'showArticle'])->name('site.article.show');
});
