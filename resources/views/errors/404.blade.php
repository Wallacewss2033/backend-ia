<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="text-center p-8 max-w-md w-full bg-white rounded-2xl shadow-sm border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">404 - Não Encontrado</h1>
        <p class="text-gray-500 mb-6">
            {{ $exception->getMessage() ?: 'O domínio acessado não existe, não está configurado ou está inativo no momento.' }}
        </p>
    </div>
</body>
</html>
