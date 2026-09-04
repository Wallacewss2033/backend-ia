<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->unique()->constrained('articles')->onDelete('cascade');
            $table->string('meta_title', 70);
            $table->string('meta_description', 160);
            $table->string('canonical_url', 500)->nullable();
            $table->string('og_image_url', 500)->nullable();
            $table->string('robots_directives', 50)->default('index, follow');
            $table->string('google_ads_conversion_label', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_metas');
    }
};
