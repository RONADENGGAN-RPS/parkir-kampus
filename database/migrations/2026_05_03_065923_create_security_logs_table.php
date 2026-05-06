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
        Schema::create('security_logs', function (Blueprint $table) {
        $table->id();
        $table->string('event_type');                                      // tipe kejadian
        $table->text('details')->nullable();                               // detail kejadian (panjang)
        $table->string('ip_address', 45);                                  // alamat IP
        // user_id bisa null, jika user belum login atau dihapus set null
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        $table->string('severity');                                        // tingkat keparahan (info, warning, error)
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
