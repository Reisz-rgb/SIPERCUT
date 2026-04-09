<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\SafeMigration;

return new class extends SafeMigration
{
    public function up(): void
    {
        $this->safeAddColumn('users', 'join_date', function (Blueprint $table) {
            $table->date('join_date')->nullable()->after('usia');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('join_date');
            $table->integer('annual_leave_quota')->default(12)->after('status');
        });
    }

    public function down(): void
    {
        $this->safeDropColumns('users', ['join_date', 'status', 'annual_leave_quota']);
    }
};