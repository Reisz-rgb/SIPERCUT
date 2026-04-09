<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\SafeMigration;

return new class extends SafeMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->safeCreateTable('cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->enum('jenis_cuti', [
                'Cuti Tahunan',
                'Cuti Besar',
                'Cuti Sakit',
                'Cuti Melahirkan',
                'Cuti Karena Alasan Penting',
                'Cuti di Luar Tanggungan Negara'
            ]);
            
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari');
            $table->text('alasan');
            $table->string('alamat_selama_cuti');
            $table->string('nomor_telepon');
            
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('keterangan_penolakan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti');
    }
};