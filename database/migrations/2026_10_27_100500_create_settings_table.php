<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sayt ayarları (açar => dəyər): telefon, e-poçt, ünvan, WhatsApp,
 * kataloq PDF faylı, "Haqqımızda" rəqəmləri (2019, 5 ölkə, 70+), ixrac ölkələri və s.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->default('general');  // general, contact, about, social
            $table->string('key')->unique();                  // contact.phone, about.founded_year
            $table->json('value')->nullable();                // sadə dəyər və ya {"az":..,"en":..,"ru":..}
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
