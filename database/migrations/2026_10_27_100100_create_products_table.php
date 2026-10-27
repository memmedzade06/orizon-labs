<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Məhsullar. Hazırda yalnız kataloq (sifariş yoxdur), ona görə qiymət
 * sahəsi istəyə bağlıdır — gələcəkdə onlayn satış üçün hazır qalır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();                         // içində məhsul olan kateqoriya silinməsin
            $table->string('slug')->unique();
            $table->string('sku', 50)->nullable()->unique();  // daxili məhsul kodu

            // Tərcümə olunan sahələr
            $table->json('name');                             // "Vitamin D3 2000 IU"
            $table->json('dosage_form')->nullable();          // "Kapsul", "Tablet", "Krem"
            $table->json('package_size')->nullable();         // "60 kapsul", "50 ml"
            $table->json('short_description')->nullable();   // kartda / siyahıda
            $table->json('description')->nullable();          // detal səhifəsi (HTML)
            $table->json('composition')->nullable();          // tərkibi
            $table->json('usage')->nullable();                // istifadə qaydası
            $table->json('warnings')->nullable();             // əks göstərişlər / xəbərdarlıq

            // Şəkil
            $table->string('image')->nullable();              // əsas şəkil

            // Kataloq / sıralama
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('popularity')->default(0); // "Populyarlığa görə" sıralama
            $table->boolean('is_new')->default(false);         // "Yeni" nişanı
            $table->boolean('is_featured')->default(false);    // "Ən çox seçilən" nişanı
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            // SEO
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();                            // səhvən silinəni bərpa etmək üçün

            $table->index(['is_active', 'category_id']);
            $table->index(['is_active', 'popularity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
