@extends('site.layout')

@section('title', 'Nossos Autores - ' . $currentSite->title)
@section('meta_description', 'Conheça os autores que escrevem para o ' . $currentSite->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Título -->
    <div class="mb-12 text-center max-w-3xl mx-auto">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl mb-4">
            Nossos <span class="text-primary">Autores</span>
        </h1>
        <p class="text-xl text-gray-500">
            Conheça a equipe de especialistas e redatores que produzem conteúdo de qualidade para você.
        </p>
    </div>

    <!-- Grid de Autores -->
    @if($authors->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($authors as $author)
                <a href="{{ route('site.author.show', ['slug' => $author->slug]) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center hover:shadow-md transition-shadow group flex flex-col items-center">
                    
                    @if($author->avatar_url)
                        <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" class="w-24 h-24 rounded-full object-cover mb-4 ring-4 ring-transparent group-hover:ring-primary/20 transition-all">
                    @else
                        <div class="w-24 h-24 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-3xl mb-4 ring-4 ring-transparent group-hover:ring-primary/20 transition-all">
                            {{ substr($author->name, 0, 1) }}
                        </div>
                    @endif
                    
                    <h2 class="text-lg font-bold text-gray-900 group-hover:text-primary transition-colors">
                        {{ $author->name }}
                    </h2>
                    
                    @if($author->bio)
                        <p class="text-sm text-gray-500 mt-2 line-clamp-3">
                            {{ $author->bio }}
                        </p>
                    @endif
                </a>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-12">
            {{ $authors->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-24 bg-white rounded-2xl shadow-sm border border-gray-100">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h2 class="mt-4 text-sm font-medium text-gray-900">Nenhum autor cadastrado</h2>
            <p class="mt-1 text-sm text-gray-500">Ainda não há autores para exibir nesta página.</p>
        </div>
    @endif
</div>
@endsection
