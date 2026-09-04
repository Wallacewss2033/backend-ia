<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteDomain extends Model
{
    use HasFactory;

    protected $table = 'sites_domains';

    protected $fillable = [
        'name',
        'title',
        'description',
        'logo_url',
        'favicon_url',
        'primary_color',
        'status',
        'dns_verified_at',
        'google_ads_id',
        'google_tag_manager_id',
    ];

    protected function casts(): array
    {
        return [
            'dns_verified_at' => 'datetime',
        ];
    }

    public function authors(): HasMany
    {
        return $this->hasMany(Author::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
