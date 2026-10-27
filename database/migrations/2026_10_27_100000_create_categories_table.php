<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Məhsul kateqoriyaları (Vitaminlər, Qida əlavələri, Kosmetika ...)
 * Tərcümə olunan sahələr JSON-dur: {"az": "...", "en": "...", "ru": "..."}
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();                 // vitaminler, qida-elaveleri
            $table->json('name');                             // {"az":"Vitaminlər","en":"Vitamins","ru":"Витамины"}
            $table->json('description')->nullable();          // "Tək və kompleks vitaminlər"
            $table->string('image')->nullable();              // storage yolu
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
