<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Xəbərlər.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('excerpt')->nullable();              // kartdakı qısa mətn
            $table->json('body')->nullable();                 // tam mətn (HTML)
            $table->json('tag')->nullable();                  // "İxrac", "Məhsul", "Tədbir"
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);   // böyük (əsas) kart
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();    // saytda göstərilən tarix

            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
