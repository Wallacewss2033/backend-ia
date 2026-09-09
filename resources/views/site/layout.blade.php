<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $currentSite->title)</title>
    
    <meta name="description" content="@yield('meta_description', $currentSite->description)">
    <link rel="canonical" href="@yield('canonical_url', request()->url())">
    
    @hasSection('og_image_url')
        <meta property="og:image" content="@yield('og_image_url')">
    @endif

    <meta name="robots" content="@yield('robots_directives', 'index, follow')">

    @if($currentSite->favicon_url)
        <link rel="icon" href="{{ $currentSite->favicon_url }}" type="image/x-icon">
    @endif

    <!-- Google Tag Manager -->
    @if($currentSite->google_tag_manager_id)
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $currentSite->google_tag_manager_id }}');</script>
    @endif
    <!-- End Google Tag Manager -->

    <!-- Google Ads -->
    @if($currentSite->google_ads_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $currentSite->google_ads_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $currentSite->google_ads_id }}');
        </script>
        @hasSection('google_ads_conversion_label')
            <script>
                gtag('event', 'conversion', {
                    'send_to': '{{ $currentSite->google_ads_id }}/@yield("google_ads_conversion_label")'
                });
            </script>
        @endif
    @endif
    <!-- End Google Ads -->

    <!-- TailwindCSS via CDN para fins de exemplo -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>

    <!-- Estilos customizados injetando primary_color -->
    <style>
        :root {
            --primary-color: {{ $currentSite->primary_color ?? '#3b82f6' }};
        }
        .text-primary { color: var(--primary-color); }
        .bg-primary { background-color: var(--primary-color); }
        .hover\:bg-primary:hover { background-color: var(--primary-color); filter: brightness(0.9); }
        .border-primary { border-color: var(--primary-color); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">
    <!-- Google Tag Manager (noscript) -->
    @if($currentSite->google_tag_manager_id)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $currentSite->google_tag_manager_id }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif
    <!-- End Google Tag Manager (noscript) -->

    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('site.index') }}" title="Página Inicial - {{ $currentSite->title }}" class="flex items-center gap-3">
                        @if($currentSite->logo_url)
                            <img src="{{ $currentSite->logo_url }}" alt="{{ $currentSite->title }}" title="{{ $currentSite->title }}" class="h-10 w-auto">
                        @else
                            <span class="text-2xl font-bold text-primary">{{ $currentSite->title }}</span>
                        @endif
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('site.index') }}" title="Página Inicial" class="text-gray-600 hover:text-primary font-medium transition-colors">Início</a>
                    @if(isset($categories))
                        @foreach($categories as $category)
                            <a href="/categoria/{{ $category->slug }}" title="Categoria: {{ $category->name }}" class="text-gray-600 hover:text-primary font-medium transition-colors">{{ $category->name }}</a>
                        @endforeach
                    @endif
                    <a href="/autores" title="Nossos Autores" class="text-gray-600 hover:text-primary font-medium transition-colors">Autores</a>
                </nav>

                <!-- Mobile Menu Button (Opcional, apenas estrutura visual) -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} {{ $currentSite->title }}. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
