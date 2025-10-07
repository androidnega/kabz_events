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
            // sender is user who sends the message (client or vendor user)
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            // receiver is the user who receives it (vendor user or client user)
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            // link to vendor for quick filtering
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->boolean('from_vendor')->default(false); // optional flag to quickly filter direction
            $table->timestamps();

            $table->index(['vendor_id', 'sender_id', 'receiver_id']);
            $table->index('read_at');
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
