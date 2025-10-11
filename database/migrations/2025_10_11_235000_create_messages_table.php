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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->string('sender_type'); // 'client' or 'vendor'
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->string('receiver_type'); // 'client' or 'vendor'
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->string('media_type')->nullable(); // 'image', 'audio', null
            $table->string('media_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('deleted_by_sender')->default(false);
            $table->boolean('deleted_by_receiver')->default(false);
            $table->timestamps();
            
            $table->index(['sender_id', 'receiver_id', 'vendor_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

