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
        Schema::create('vip_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('duration_days')->comment('30, 90, 365, etc.');
            $table->integer('image_limit')->default(20)->comment('Max portfolio images');
            $table->integer('free_ads')->default(0)->comment('Free featured ad slots per cycle');
            $table->tinyInteger('priority_level')->default(2)->comment('1=normal, 2=premium, 3=VIP');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true)->comment('Active/Inactive');
            $table->timestamps();
            
            $table->index('status');
            $table->index('priority_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vip_plans');
    }
};

