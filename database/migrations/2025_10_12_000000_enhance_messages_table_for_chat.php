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
        Schema::table('messages', function (Blueprint $table) {
            // Add new fields for chat system
            $table->string('sender_type')->after('sender_id')->default('client'); // 'client' or 'vendor'
            $table->string('receiver_type')->after('receiver_id')->default('client'); // 'client' or 'vendor'
            $table->string('media_type')->nullable()->after('message'); // 'image', 'audio', null
            $table->string('media_url')->nullable()->after('media_type');
            $table->boolean('is_read')->default(false)->after('media_url');
            $table->boolean('deleted_by_sender')->default(false)->after('read_at');
            $table->boolean('deleted_by_receiver')->default(false)->after('deleted_by_sender');
            
            // Modify message to be nullable since we can have media-only messages
            $table->text('message')->nullable()->change();
            
            // Add indexes for better performance
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'sender_type',
                'receiver_type',
                'media_type',
                'media_url',
                'is_read',
                'deleted_by_sender',
                'deleted_by_receiver'
            ]);
            $table->dropIndex(['created_at']);
        });
    }
};

