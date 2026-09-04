<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_domain_id')->constrained('sites_domains')->onDelete('cascade');
            $table->foreignId('author_id')->nullable()->constrained('authors')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('featured_image_url', 500)->nullable();
            $table->string('featured_image_alt', 255)->nullable();
            $table->enum('status', ['draft', 'in_review', 'approved', 'published', 'rejected'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['site_domain_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
