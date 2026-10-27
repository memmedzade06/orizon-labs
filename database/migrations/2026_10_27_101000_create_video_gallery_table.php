<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Video qalereya. Videolar YouTube (və ya Vimeo) linki ilə əlavə olunur —
 * hostinqdə böyük video faylları saxlamağa ehtiyac qalmır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_gallery', function (Blueprint $table) {
            $table->id();
            $table->json('title');                            // video başlığı: {"az":"...","en":"...","ru":"..."}
            $table->string('video_url');                      // https://www.youtube.com/watch?v=...
            $table->string('thumbnail')->nullable();          // önizləmə şəkli; boşdursa YouTube-un öz şəkli
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_gallery');
    }
};
