<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_domain_id')->constrained('sites_domains')->onDelete('cascade');
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->text('bio')->nullable();
            $table->string('avatar_url', 500)->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();

            $table->unique(['site_domain_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
