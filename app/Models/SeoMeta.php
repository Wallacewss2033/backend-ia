<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_image_url',
        'robots_directives',
        'google_ads_conversion_label',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
