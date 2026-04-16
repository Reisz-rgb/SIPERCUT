<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Support\SafeMigration;

return new class extends SafeMigration
{
    public function up(): void
    {
        $this->safeCreateTable('leave_requests', function (Blueprint $table) {
            $table->string('emergency_phone')->nullable()->after('phone');
            $table->string('emergency_relationship', 100)->nullable()->after('emergency_phone');
        });
    }

    public function down(): void
    {
        $this->safeCreateTable('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['emergency_phone', 'emergency_relationship']);
        });
    }
};
