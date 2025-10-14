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
        Schema::create('featured_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->enum('placement', ['homepage', 'category', 'search'])->default('search');
            $table->string('headline', 100);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'active', 'expired', 'suspended'])->default('pending');
            $table->decimal('price', 10, 2);
            $table->string('payment_ref')->nullable();
            $table->integer('views')->default(0);
            $table->integer('clicks')->default(0);
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->index('vendor_id');
            $table->index('service_id');
            $table->index('placement');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_ads');
    }
};

