<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\SafeMigration;

return new class extends SafeMigration
{
    public function up(): void
    {
        $this->safeCreateTable('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('year'); // Tahun
            $table->integer('quota')->default(12); // Kuota awal tahun ini
            $table->integer('used')->default(0); // Sudah digunakan
            $table->integer('remaining')->default(12); // Sisa
            $table->timestamps();
            
            $table->unique(['user_id', 'year']); // Satu user satu record per tahun
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};