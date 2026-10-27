<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Saytdan gələn müraciətlər: "Əlaqə" və "Tərəfdaşlıq üçün yazın" formaları.
 * Admin paneldə oxunur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('contact');   // contact, partner
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('message');
            $table->string('locale', 5)->nullable();          // hansı dildə yazılıb
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('read_at')->nullable();         // null = oxunmayıb
            $table->timestamps();

            $table->index(['type', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
