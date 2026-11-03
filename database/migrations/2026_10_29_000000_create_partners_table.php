<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tərəfdaş brendlər — "Aptek, klinika və distribyutorlar üçün" bölməsinin yanında göstərilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();                // brend adı (alt mətn / başlıq üçün)
            $table->string('logo');                            // loqo şəklinin yolu
            $table->string('url')->nullable();                 // brendin veb-saytı (istəyə bağlı)
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
