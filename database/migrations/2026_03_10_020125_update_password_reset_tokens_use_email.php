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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Hapus kolom phone jika ada
            if (Schema::hasColumn('password_reset_tokens', 'phone')) {
                $table->dropColumn('phone');
            }
            // Tambah email jika belum ada
            if (!Schema::hasColumn('password_reset_tokens', 'email')) {
                $table->string('email')->primary();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
