<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\SiteDomain;
use Illuminate\Support\Str;

class ContentManagerTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Gerenciar (criar e visualizar) Artigos, Autores, Categorias e Domínios de Site (SiteDomain).';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $entity = $request['entity'] ?? null;
        $action = $request['action'] ?? null;

        if ($entity === 'article') {
            if ($action === 'create') {
                $title = $request['title'] ?? null;
                $siteDomainId = $request['site_domain_id'] ?? null;
                $content = $request['content'] ?? null;

                if (!$title || !$siteDomainId || !$content) {
                    return 'Erro: title, site_domain_id e content são obrigatórios para criar um artigo.';
                }

                $baseSlug = Str::slug($title);
                $slug = $baseSlug;
                $count = 1;
                while (Article::where('site_domain_id', $siteDomainId)->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }

                $article = Article::create([
                    'site_domain_id' => $siteDomainId,
                    'author_id' => $request['author_id'] ?? null,
                    'category_id' => $request['category_id'] ?? null,
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                ]);
                return "Artigo '{$article->title}' criado com sucesso com ID {$article->id}.";
            }
            if ($action === 'update') {
                $articleId = $request['article_id'] ?? null;
                if (!$articleId) return 'É necessário informar o article_id para atualizar.';
                
                $article = Article::find($articleId);
                if (!$article) return 'Artigo não encontrado para atualização.';

                if (isset($request['title'])) {
                    $article->title = $request['title'];
                    $baseSlug = Str::slug($request['title']);
                    $slug = $baseSlug;
                    $count = 1;
                    while (Article::where('site_domain_id', $article->site_domain_id)->where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                        $slug = $baseSlug . '-' . $count;
                        $count++;
                    }
                    $article->slug = $slug;
                }
                if (isset($request['content'])) $article->content = $request['content'];
                if (isset($request['author_id'])) $article->author_id = $request['author_id'];
                if (isset($request['category_id'])) $article->category_id = $request['category_id'];
                $article->save();

                return "Artigo '{$article->title}' (ID {$article->id}) atualizado com sucesso.";
            }
            if ($action === 'count') {
                $query = Article::query();
                if (isset($request['author_name'])) {
                    $author = Author::where('name', 'like', '%' . $request['author_name'] . '%')->first();
                    if ($author) {
                        $query->where('author_id', $author->id);
                        return "O autor {$author->name} tem " . $query->count() . " artigo(s).";
                    }
                    return "Autor '{$request['author_name']}' não encontrado.";
                }
                return "Total de artigos: " . $query->count();
            }
        }

        if ($entity === 'author') {
            if ($action === 'create') {
                $name = $request['name'] ?? null;
                $siteDomainId = $request['site_domain_id'] ?? null;

                if (!$name || !$siteDomainId) {
                    return 'Erro: name e site_domain_id são obrigatórios para criar um autor.';
                }

                $baseSlug = Str::slug($name);
                $slug = $baseSlug;
                $count = 1;
                while (Author::where('site_domain_id', $siteDomainId)->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }

                $author = Author::create([
                    'site_domain_id' => $siteDomainId,
                    'name' => $name,
                    'slug' => $slug,
                ]);
                return "Autor '{$author->name}' criado com sucesso com ID {$author->id}.";
            }
            if ($action === 'get') {
                $author = Author::where('name', 'like', '%' . $request['name'] . '%')->first();
                if ($author) {
                    return "Autor: {$author->name} (ID: {$author->id}) | Bio: {$author->bio}";
                }
                return "Autor não encontrado.";
            }
        }

        if ($entity === 'category') {
            if ($action === 'create') {
                $name = $request['name'] ?? null;
                $siteDomainId = $request['site_domain_id'] ?? null;

                if (!$name || !$siteDomainId) {
                    return 'Erro: name e site_domain_id são obrigatórios para criar uma categoria.';
                }

                $baseSlug = Str::slug($name);
                $slug = $baseSlug;
                $count = 1;
                while (Category::where('site_domain_id', $siteDomainId)->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }

                $category = Category::create([
                    'site_domain_id' => $siteDomainId,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $request['description'] ?? null,
                ]);
                return "Categoria '{$category->name}' criada com sucesso com ID {$category->id}.";
            }
            if ($action === 'get') {
                $category = Category::where('name', 'like', '%' . $request['name'] . '%')->first();
                if ($category) {
                    return "Categoria: {$category->name} (ID: {$category->id}) | Descrição: {$category->description}";
                }
                return "Categoria não encontrada.";
            }
        }

        if ($entity === 'site_domain') {
            if ($action === 'create') {
                $domainUrl = $request['domain_url'] ?? null;
                $title = $request['title'] ?? null;

                if (!$domainUrl || !$title) {
                    return 'Erro: domain_url e title são obrigatórios para criar um domínio.';
                }

                $domain = SiteDomain::create([
                    'domain_url' => $domainUrl,
                    'title' => $title,
                ]);
                return "Domínio '{$domain->domain_url}' criado com sucesso com ID {$domain->id}.";
            }
            if ($action === 'get') {
                $domain = SiteDomain::where('domain_url', $request['domain_url'])->first();
                if ($domain) {
                    return "Domínio: {$domain->domain_url} (ID: {$domain->id}) | Título: {$domain->title}";
                }
                return "Domínio não encontrado.";
            }
        }

        return "Ação ou entidade não reconhecida.";
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'entity' => $schema->string()
                ->enum(['article', 'author', 'category', 'site_domain'])
                ->description('A entidade a ser manipulada: article, author, category ou site_domain.')
                ->required(),
            
            'action' => $schema->string()
                ->enum(['create', 'get', 'count', 'update'])
                ->description('A ação a ser realizada: create, get, count, update.')
                ->required(),

            // Parâmetros para criar ou buscar Artigo
            'article_id' => $schema->integer()->description('ID do artigo para atualizar (Obrigatório para update).'),
            'title' => $schema->string()->description('Título do artigo (Obrigatório para criar artigo).'),
            'content' => $schema->string()->description('Conteúdo do artigo (Obrigatório para criar artigo).'),
            'author_name' => $schema->string()->description('Nome do autor para filtrar contagem de artigos.'),

            // Parâmetros para criar ou buscar Autor
            'name' => $schema->string()->description('Nome (Obrigatório para criar ou buscar autor/categoria).'),
            
            // Parâmetros para criar ou buscar Categoria
            'description' => $schema->string()->description('Descrição da categoria (Opcional).'),
            
            // Parâmetros para criar ou buscar SiteDomain
            'domain_url' => $schema->string()->description('URL do domínio (Obrigatório para criar ou buscar domínio).'),
            
            // Relacionamentos comuns
            'site_domain_id' => $schema->integer()->description('ID do domínio do site (Obrigatório para criar artigo, autor ou categoria).'),
            'author_id' => $schema->integer()->description('ID do autor (Opcional para artigo).'),
            'category_id' => $schema->integer()->description('ID da categoria (Opcional para artigo).'),
        ];
    }
}
