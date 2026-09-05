@extends('site.layout')

@php
    $pageTitle = $article->seoMeta->meta_title ?? $article->title;
    $pageDescription = $article->seoMeta->meta_description ?? $article->summary;
    $pageCanonical = $article->seoMeta->canonical_url ?? request()->url();
@endphp

@section('title', $pageTitle . ' - ' . $currentSite->title)
@section('meta_description', $pageDescription)
@section('canonical_url', $pageCanonical)

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Header do Artigo -->
    <header class="mb-10 text-center">
        @if($article->category)
            <a href="{{ route('site.category.show', ['slug' => $article->category->slug]) }}" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-primary/10 text-primary hover:bg-primary/20 transition-colors mb-6">
                {{ $article->category->name }}
            </a>
        @endif

        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-6">
            {{ $article->title }}
        </h1>

        @if($article->summary)
            <p class="text-xl text-gray-500 mb-8 max-w-3xl mx-auto leading-relaxed">
                {{ $article->summary }}
            </p>
        @endif

        <div class="flex items-center justify-center gap-4 text-gray-500 text-sm">
            @if($article->author)
                <a href="{{ route('site.author.show', ['slug' => $article->author->slug]) }}" class="flex items-center gap-2 hover:text-primary transition-colors">
                    @if($article->author->avatar_url)
                        <img src="{{ $article->author->avatar_url }}" alt="{{ $article->author->name }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                            {{ substr($article->author->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="font-medium text-gray-900">{{ $article->author->name }}</span>
                </a>
                <span class="text-gray-300">•</span>
            @endif
            <time datetime="{{ $article->published_at }}">
                {{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->translatedFormat('d \d\e F \d\e Y') : 'Rascunho' }}
            </time>
        </div>
    </header>

    <!-- Imagem de Destaque -->
    @if($article->featured_image_url)
        <figure class="mb-12">
            <img src="{{ $article->featured_image_url }}" alt="{{ $article->featured_image_alt ?? $article->title }}" class="w-full h-auto rounded-3xl shadow-sm object-cover max-h-[600px]">
            @if($article->featured_image_alt)
                <figcaption class="text-center text-sm text-gray-500 mt-3">{{ $article->featured_image_alt }}</figcaption>
            @endif
        </figure>
    @endif

    <!-- Conteúdo do Artigo -->
    <!-- Adicionamos classes prose do Tailwind via CDN para estilização automática do HTML -->
    <div class="prose prose-lg md:prose-xl prose-primary mx-auto prose-img:rounded-xl">
        {!! $article->content !!}
    </div>

    <!-- Seção do Autor no final -->
    @if($article->author)
        <div class="mt-16 pt-10 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 bg-gray-50 rounded-2xl p-8">
                @if($article->author->avatar_url)
                    <img src="{{ $article->author->avatar_url }}" alt="{{ $article->author->name }}" class="w-20 h-20 rounded-full object-cover">
                @else
                    <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-2xl">
                        {{ substr($article->author->name, 0, 1) }}
                    </div>
                @endif
                <div class="text-center sm:text-left">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Escrito por {{ $article->author->name }}</h3>
                    @if($article->author->bio)
                        <p class="text-gray-600 mb-4">{{ $article->author->bio }}</p>
                    @endif
                    <a href="{{ route('site.author.show', ['slug' => $article->author->slug]) }}" class="inline-flex items-center text-sm font-semibold text-primary hover:text-primary/80">
                        Ver todos os artigos &rarr;
                    </a>
                </div>
            </div>
        </div>
    @endif
</article>
@endsection
