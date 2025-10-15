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
        Schema::table('vendor_subscriptions', function (Blueprint $table) {
            // Payment tracking
            $table->string('payment_status')->default('pending')->after('status'); // pending, paid, failed
            $table->string('payment_method')->nullable()->after('payment_status'); // paystack, manual
            $table->string('paystack_reference')->nullable()->unique()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('paystack_reference');
            
            // Approval workflow
            $table->string('approval_status')->default('pending')->after('paid_at'); // pending, approved, rejected
            $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_note')->nullable()->after('approved_at');
            $table->timestamp('payment_expires_at')->nullable()->after('approval_note'); // For auto-approval
            
            // Indexes
            $table->index('payment_status');
            $table->index('approval_status');
            $table->index('payment_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'paystack_reference',
                'paid_at',
                'approval_status',
                'approved_by',
                'approved_at',
                'approval_note',
                'payment_expires_at',
            ]);
        });
    }
};
