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
        Schema::table('users', function (Blueprint $table) {
            // Status keaktifan user
            $table->boolean('active')->default(true)->after('email_verified_at');

            // Foreign key ke tabel roles (Super Admin, Admin, dll.)
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();

            // Kolom tambahan profil
            $table->string('phone')->nullable();
            $table->string('nim')->nullable()->unique();
            $table->string('avatar')->nullable();

            // Keamanan & login tracking
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            // User tracking (siapa yang membuat/mengubah)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Soft delete
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key dulu sebelum drop kolom role_id
            $table->dropForeign(['role_id']);

            // Hapus semua kolom yang ditambahkan
            $table->dropColumn([
                'active',
                'role_id',
                'phone',
                'nim',
                'avatar',
                'last_login_at',
                'last_login_ip',
                'login_attempts',
                'locked_until',
                'created_by',
                'updated_by',
                'deleted_at', // soft delete
            ]);
        });
    }
};