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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('code', 6);
            $table->enum('type', ['registration', 'password_reset', 'verification'])->default('registration');
            $table->boolean('used')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('phone_number');
            $table->index(['phone_number', 'code']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
