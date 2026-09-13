@extends('site.layout')
@section('title', 'Termos de Uso - ' . $currentSite->title)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 prose prose-lg">
    <h1>Termos de Uso</h1>
    <p>Ao acessar ao site <strong>{{ $currentSite->title }}</strong>, concorda em cumprir estes termos de serviço, todas as leis e regulamentos aplicáveis e concorda que é responsável pelo cumprimento de todas as leis locais aplicáveis.</p>
    
    <h2>Uso do Portal e Propriedade Intelectual</h2>
    <p>Os conteúdos, textos, imagens e materiais publicados neste site são de propriedade do <strong>{{ $currentSite->title }}</strong> ou estão devidamente licenciados. É proibida a reprodução, cópia ou distribuição comercial sem autorização prévia, respeitando as leis de direitos autorais.</p>
    
    <h2>Isenção de Responsabilidade</h2>
    <p>Os materiais no site são fornecidos "como estão". Não oferecemos garantias, expressas ou implícitas, e nos isentamos de qualquer responsabilidade por eventuais danos no uso ou na incapacidade de uso dos materiais do site. Também não nos responsabilizamos pelo conteúdo de sites externos vinculados por links em nossas páginas.</p>

    <h2>Jurisdição Legal</h2>
    <p>Estes termos e condições são regidos e interpretados de acordo com as leis do país/estado de operação do site e você se submete irrevogavelmente à jurisdição exclusiva dos tribunais daquela localidade para resolução de quaisquer disputas.</p>
</div>
@endsection
