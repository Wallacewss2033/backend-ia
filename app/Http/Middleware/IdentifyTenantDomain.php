<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SiteDomain;
use Illuminate\Support\Facades\View;

class IdentifyTenantDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pega o host da requisição (ex: www.meusite.com.br ou app.meusite.com.br)
        $host = $request->getHost();

        // Normalização: remove "www." para consulta, pois no banco mantemos limpo
        $cleanHost = preg_replace('/^www\./', '', $host);

        // Opcional: Se estiver testando localmente, pode querer ignorar localhost ou setar um default
        // if ($cleanHost === 'localhost' || $cleanHost === '127.0.0.1') {
        //     $cleanHost = 'site-exemplo.com.br'; // Para ambiente de dev
        // }

        // Consulta o banco procurando o domínio exato e verificando se está aprovado
        $site = SiteDomain::where('domain_url', $cleanHost)
            ->where('status', 'approved')
            ->first();

        // Se o domínio não existir no banco ou não estiver aprovado, retorna erro 404
        if (!$site) {
            abort(404, 'O domínio acessado não foi encontrado ou não está ativo no sistema.');
        }

        // Se encontrou, armazena o modelo na Request para uso nos Controllers
        $request->attributes->set('current_site', $site);

        // Compartilha a variável 'currentSite' globalmente com todas as Views Blade
        View::share('currentSite', $site);

        return $next($request);
    }
}
