<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sertifikatlar / lisenziyalar — saytın sol kənarındakı üzən zolaqda göstərilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('image');                          // sertifikat şəklinin yolu
            $table->json('title')->nullable();                // ad: {"az":"...","en":"...","ru":"..."}
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
