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
        Schema::create('vendor_response_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('client_message_id')->constrained('messages')->onDelete('cascade');
            $table->foreignId('vendor_reply_id')->constrained('messages')->onDelete('cascade');
            $table->integer('response_time_minutes'); // Time taken to respond in minutes
            $table->timestamps();
            
            $table->index('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_response_times');
    }
};

