@extends('site.layout')

@section('title', $currentCategory->name . ' - ' . $currentSite->title)
@section('meta_description', 'Artigos na categoria ' . $currentCategory->name . ' em ' . $currentSite->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Título da Categoria -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl mb-2">
            Categoria: <span class="text-primary">{{ $currentCategory->name }}</span>
        </h1>
        <p class="text-xl text-gray-500">
            Explorando artigos sobre {{ $currentCategory->name }}
        </p>
    </div>

    <!-- Grid de Artigos Recentes -->
    @if($articles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($articles as $article)
                <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col h-full">
                    <!-- Imagem de Destaque -->
                    <a href="{{ route('site.article.show', ['slug' => $article->slug]) }}" class="block relative h-48 w-full bg-gray-100 shrink-0">
                        @if($article->featured_image_url)
                            <img src="{{ $article->featured_image_url }}" alt="{{ $article->featured_image_alt ?? $article->title }}" class="absolute inset-0 w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </a>

                    <div class="p-6 flex flex-col flex-grow">
                        <!-- Categoria e Data -->
                        <div class="flex items-center gap-3 mb-3 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                {{ $article->category->name }}
                            </span>
                            <span class="text-gray-500">{{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('d/m/Y') : 'Rascunho' }}</span>
                        </div>

                        <!-- Título -->
                        <h2 class="text-xl font-bold text-gray-900 mb-2 leading-tight">
                            <a href="{{ route('site.article.show', ['slug' => $article->slug]) }}" class="hover:text-primary transition-colors">
                                {{ $article->title }}
                            </a>
                        </h2>

                        <!-- Resumo -->
                        <p class="text-gray-600 mb-4 flex-grow line-clamp-3">
                            {{ $article->summary ?? Str::limit(strip_tags($article->content), 120) }}
                        </p>

                        <!-- Autor -->
                        @if($article->author)
                            <div class="mt-auto pt-4 border-t border-gray-100 flex items-center gap-3">
                                @if($article->author->avatar_url)
                                    <img src="{{ $article->author->avatar_url }}" alt="{{ $article->author->name }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xs">
                                        {{ substr($article->author->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-900">{{ $article->author->name }}</span>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-12">
            {{ $articles->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-24 bg-white rounded-2xl shadow-sm border border-gray-100">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <h2 class="mt-4 text-sm font-medium text-gray-900">Nenhum artigo encontrado nesta categoria</h2>
            <p class="mt-1 text-sm text-gray-500">Tente explorar outras categorias no menu acima.</p>
        </div>
    @endif
</div>
@endsection
