<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Şəkil qalereyası.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photo_gallery', function (Blueprint $table) {
            $table->id();
            $table->string('image');                          // yüklənmiş şəklin yolu
            $table->json('title')->nullable();                // şəkil altı yazı: {"az":"...","en":"...","ru":"..."}
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_gallery');
    }
};
