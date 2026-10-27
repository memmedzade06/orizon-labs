<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ana səhifədəki slayder (istehsalat mərhələləri).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->json('eyebrow')->nullable();              // "İstehsalat · 1-ci mərhələ"
            $table->json('title');
            $table->json('text')->nullable();
            $table->string('image')->nullable();              // real foto
            $table->json('button_text')->nullable();          // "Məhsullara bax"
            $table->string('button_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
