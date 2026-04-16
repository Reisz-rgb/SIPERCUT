<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\SafeMigration;

return new class extends SafeMigration
{
    public function up(): void
    {
        $this->safeCreateTable('leave_requests', function (Blueprint $table) {
            // Tambah kolom yang kurang
            $table->integer('duration')->after('end_date'); // Jumlah hari
            $table->string('address')->nullable()->after('reason'); // Alamat selama cuti
            $table->string('phone')->nullable()->after('address'); // Nomor telepon
            $table->text('notes')->nullable()->after('phone'); // Catatan tambahan
            $table->string('file_path')->nullable()->after('notes'); // Path file upload
            $table->text('rejection_reason')->nullable()->after('status'); // Alasan penolakan
            
            // Rename kolom agar konsisten
            $table->renameColumn('leave_type', 'jenis_cuti');
        });
    }

    public function down(): void
    {
        $this->safeCreateTable('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['duration', 'address', 'phone', 'notes', 'file_path', 'rejection_reason']);
            $table->renameColumn('jenis_cuti', 'leave_type');
        });
    }
};