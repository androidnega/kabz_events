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
        Schema::table('verification_requests', function (Blueprint $table) {
            // Admin audit trail
            $table->unsignedBigInteger('decided_by')->nullable()->after('decided_at');
            $table->foreign('decided_by')->references('id')->on('users')->onDelete('set null');
            $table->index('decided_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            $table->dropForeign(['decided_by']);
            $table->dropColumn('decided_by');
        });
    }
};
