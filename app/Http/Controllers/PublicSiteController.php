<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Article;
use App\Models\Author;
use Illuminate\View\View;

class PublicSiteController extends Controller
{
    /**
     * Renderiza a página inicial (Home) do portal de notícias/site.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\SiteDomain $site */
        $site = $request->attributes->get('current_site');

        // Carrega categorias do domínio, ordenadas por nome para montar o menu
        $categories = Category::where('site_domain_id', $site->id)
            ->orderBy('name')
            ->get();

        // Carrega os artigos publicados, usando eager loading para autor e categoria para evitar N+1
        $articles = Article::with(['author', 'category'])
            ->where('site_domain_id', $site->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('site.index', compact('categories', 'articles'));
    }

    /**
     * Renderiza a página de uma categoria específica.
     */
    public function showCategory(Request $request, string $slug): View
    {
        /** @var \App\Models\SiteDomain $site */
        $site = $request->attributes->get('current_site');

        $categories = Category::where('site_domain_id', $site->id)->orderBy('name')->get();

        $currentCategory = Category::where('site_domain_id', $site->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $articles = Article::with(['author', 'category'])
            ->where('site_domain_id', $site->id)
            ->where('category_id', $currentCategory->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('site.category', compact('categories', 'currentCategory', 'articles'));
    }

    /**
     * Renderiza a página de listagem de autores.
     */
    public function indexAuthors(Request $request): View
    {
        /** @var \App\Models\SiteDomain $site */
        $site = $request->attributes->get('current_site');

        $categories = Category::where('site_domain_id', $site->id)->orderBy('name')->get();

        $authors = Author::where('site_domain_id', $site->id)
            ->orderBy('name')
            ->paginate(12);

        return view('site.authors', compact('categories', 'authors'));
    }

    /**
     * Renderiza a página de um autor específico.
     */
    public function showAuthor(Request $request, string $slug): View
    {
        /** @var \App\Models\SiteDomain $site */
        $site = $request->attributes->get('current_site');

        $categories = Category::where('site_domain_id', $site->id)->orderBy('name')->get();

        $currentAuthor = Author::where('site_domain_id', $site->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $articles = Article::with(['author', 'category'])
            ->where('site_domain_id', $site->id)
            ->where('author_id', $currentAuthor->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('site.author', compact('categories', 'currentAuthor', 'articles'));
    }

    /**
     * Placeholder para exibir um artigo individual.
     */
    public function showArticle(Request $request, string $slug): View
    {
        /** @var \App\Models\SiteDomain $site */
        $site = $request->attributes->get('current_site');

        $article = Article::with(['author', 'category', 'seoMeta'])
            ->where('site_domain_id', $site->id)
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $categories = Category::where('site_domain_id', $site->id)->orderBy('name')->get();

        return view('site.article', compact('article', 'categories'));
    }

    /**
     * Retorna o arquivo ads.txt dinâmico para o Google Adsense.
     */
    public function adsTxt(Request $request)
    {
        /** @var \App\Models\SiteDomain $site */
        $site = $request->attributes->get('current_site');

        if (!$site || empty($site->google_adsense_id)) {
            abort(404);
        }

        // Garante que o prefixo "ca-" não fique duplicado ou se o usuário inserir ca-pub-
        $adsenseId = str_replace('ca-', '', $site->google_adsense_id);

        $content = "google.com, {$adsenseId}, DIRECT, f08c47fec0942fa0";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function privacyPolicy(Request $request): View
    {
        $categories = Category::where('site_domain_id', $request->attributes->get('current_site')->id)->orderBy('name')->get();
        return view('site.privacy', compact('categories'));
    }

    public function termsOfUse(Request $request): View
    {
        $categories = Category::where('site_domain_id', $request->attributes->get('current_site')->id)->orderBy('name')->get();
        return view('site.terms', compact('categories'));
    }

    public function aboutUs(Request $request): View
    {
        $categories = Category::where('site_domain_id', $request->attributes->get('current_site')->id)->orderBy('name')->get();
        return view('site.about', compact('categories'));
    }

    public function contact(Request $request): View
    {
        $categories = Category::where('site_domain_id', $request->attributes->get('current_site')->id)->orderBy('name')->get();
        return view('site.contact', compact('categories'));
    }
}
