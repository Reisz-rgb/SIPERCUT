<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('emergency_phone')->nullable()->after('phone');
            $table->string('emergency_relationship', 100)->nullable()->after('emergency_phone');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['emergency_phone', 'emergency_relationship']);
        });
    }
};
