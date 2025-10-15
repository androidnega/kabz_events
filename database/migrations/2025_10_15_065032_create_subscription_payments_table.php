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
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_subscription_id')->constrained()->onDelete('cascade');
            $table->string('paystack_reference')->unique();
            $table->string('payment_status')->default('pending'); // pending, success, failed
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('GHS');
            $table->string('payment_channel')->nullable(); // card, mobile_money, bank
            $table->text('customer_email');
            $table->text('metadata')->nullable(); // JSON data from Paystack
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index('paystack_reference');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
