<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();              // nama pengaturan, harus unik
            $table->text('value')->nullable();            // nilainya (teks panjang), bisa kosong
            $table->string('type')->default('string');    // tipe data (string, integer, boolean, json, dll)
            $table->string('description')->nullable();    // deskripsi singkat pengaturan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
