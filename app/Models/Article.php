<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_domain_id',
        'author_id',
        'category_id',
        'title',
        'slug',
        'summary',
        'content',
        'featured_image_url',
        'featured_image_alt',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function siteDomain(): BelongsTo
    {
        return $this->belongsTo(SiteDomain::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function seoMeta(): HasOne
    {
        return $this->hasOne(SeoMeta::class);
    }
}
