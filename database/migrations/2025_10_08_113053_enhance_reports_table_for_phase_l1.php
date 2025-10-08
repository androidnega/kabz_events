<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Add new fields for Phase L1
            $table->foreignId('target_id')->nullable()->after('vendor_id')->constrained('users')->onDelete('cascade');
            $table->enum('target_type', ['vendor', 'client', 'service', 'other'])->nullable()->after('target_id');
            $table->text('admin_note')->nullable()->after('admin_response');
            
            // Add indexes
            $table->index('target_id');
            $table->index('target_type');
        });

        // Update status enum to include 'in_review' and 'pending'
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('pending', 'open', 'in_review', 'resolved') DEFAULT 'pending'");
        
        // Migrate existing 'open' records to 'pending' for consistency
        DB::table('reports')->where('status', 'open')->update(['status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status enum back to original
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('open', 'resolved') DEFAULT 'open'");
        
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['target_id']);
            $table->dropIndex(['target_id']);
            $table->dropIndex(['target_type']);
            $table->dropColumn(['target_id', 'target_type', 'admin_note']);
        });
    }
};
