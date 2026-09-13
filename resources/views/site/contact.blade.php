@extends('site.layout')
@section('title', 'Contato - ' . $currentSite->title)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="prose prose-lg mb-8">
        <h1>Entre em Contato</h1>
        <p>Quer falar com a nossa equipe? Preencha o formulário abaixo ou utilize um de nossos canais diretos.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div>
            <form action="#" method="POST" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                    <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2 border" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                    <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2 border" required>
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Mensagem</label>
                    <textarea id="message" name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm p-2 border" required></textarea>
                </div>
                <button type="button" class="bg-primary text-white py-2 px-4 rounded hover:bg-opacity-90 transition-all font-medium" onclick="alert('Mensagem enviada com sucesso! Logo entraremos em contato.')">Enviar Mensagem</button>
            </form>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Canais de Atendimento</h3>
            <div class="space-y-4 text-gray-600">
                <p><strong>E-mail:</strong><br> <a href="mailto:contato@{{ $currentSite->domain_url }}" class="text-primary hover:underline">contato@{{ $currentSite->domain_url }}</a></p>
                <p><strong>Redes Sociais:</strong><br> Acompanhe o <strong>{{ $currentSite->title }}</strong> em nossas redes oficiais para novidades.</p>
                <p class="text-sm mt-4 text-gray-500">O nosso tempo médio de resposta é de até 48 horas úteis. Se for um assunto urgente, por favor, insira "URGENTE" no assunto da sua mensagem.</p>
            </div>
        </div>
    </div>
</div>
@endsection
