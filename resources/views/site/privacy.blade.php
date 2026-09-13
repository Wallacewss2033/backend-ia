@extends('site.layout')
@section('title', 'Política de Privacidade - ' . $currentSite->title)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 prose prose-lg">
    <h1>Política de Privacidade</h1>
    <p>A sua privacidade é importante para nós. É política do <strong>{{ $currentSite->title }}</strong> respeitar a sua privacidade em relação a qualquer informação sua que possamos coletar no site.</p>
    
    <h2>Coleta de Dados e Cookies</h2>
    <p>Utilizamos cookies de terceiros, incluindo cookies do Google AdSense e DoubleClick, para veicular anúncios mais relevantes com base nas suas visitas anteriores ao nosso site ou a outros sites na internet.</p>
    <p>Os cookies de publicidade do Google permitem que ele e seus parceiros veiculem anúncios com base na sua navegação. Você pode desativar o uso de cookies personalizados acessando as <a href="https://myadcenter.google.com/" target="_blank" rel="nofollow">Configurações de Anúncios do Google</a>.</p>

    <h2>LGPD e GDPR</h2>
    <p>Em conformidade com a Lei Geral de Proteção de Dados (LGPD) e o Regulamento Geral sobre a Proteção de Dados (GDPR), garantimos a transparência no uso dos seus dados. A finalidade dos dados coletados (como cookies de navegação) é estritamente para métricas de tráfego, segurança e veiculação de anúncios (como descrito acima). Não vendemos suas informações pessoais para terceiros.</p>
    
    <h2>Como desativar os cookies</h2>
    <p>Além das configurações do Google, você pode gerenciar e desativar cookies diretamente nas configurações do seu navegador.</p>
</div>
@endsection
