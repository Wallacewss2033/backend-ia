@extends('site.layout')
@section('title', 'Sobre Nós - ' . $currentSite->title)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 prose prose-lg">
    <h1>Quem Somos</h1>
    <p>Bem-vindo ao <strong>{{ $currentSite->title }}</strong>!</p>
    
    <h2>Nossa Missão</h2>
    <p>Nossa missão editorial é trazer conteúdo de qualidade, confiável e atualizado para nossos leitores. Trabalhamos diariamente para garantir que as informações publicadas sejam úteis, imparciais e relevantes para a nossa audiência.</p>
    
    <h2>Nossa Equipe</h2>
    <p>O <strong>{{ $currentSite->title }}</strong> é mantido por uma equipe de criadores e profissionais apaixonados pelo que fazem, dedicados a construir um ecossistema de informações seguro e acessível.</p>

    @if($currentSite->publisher_name)
    <h2>Informações da Empresa</h2>
    <p>O portal é operado por: <strong>{{ $currentSite->publisher_name }}</strong>.</p>
    @endif
</div>
@endsection
