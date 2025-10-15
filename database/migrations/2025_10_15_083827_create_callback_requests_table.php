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
        Schema::create('callback_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index('vendor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('callback_requests');
    }
};
