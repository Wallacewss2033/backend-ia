<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain_url', 255)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('favicon_url', 500)->nullable();
            $table->string('primary_color', 7)->default('#000000');
            $table->enum('status', ['draft', 'in_review', 'approved', 'rejected', 'suspended'])->default('draft');
            $table->timestamp('dns_verified_at')->nullable();
            $table->string('google_ads_id', 50)->nullable();
            $table->string('google_tag_manager_id', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites_domains');
    }
};
