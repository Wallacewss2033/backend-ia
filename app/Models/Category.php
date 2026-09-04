<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_domain_id',
        'name',
        'slug',
        'description',
    ];

    public function siteDomain(): BelongsTo
    {
        return $this->belongsTo(SiteDomain::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
