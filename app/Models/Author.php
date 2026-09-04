<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_domain_id',
        'name',
        'slug',
        'bio',
        'avatar_url',
        'social_links',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }

    public function siteDomain(): BelongsTo
    {
        return $this->belongsTo(SiteDomain::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
