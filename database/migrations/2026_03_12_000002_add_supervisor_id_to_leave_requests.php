<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\SafeMigration;

return new class extends SafeMigration
{
    public function up(): void
    {
        $this->safeCreateTable('leave_requests', function (Blueprint $table) {
            // Letakkan setelah kolom 'user_id' agar urutannya logis
            $table->foreignId('supervisor_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('supervisors')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        $this->safeCreateTable('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }
};
