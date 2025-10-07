<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // view, click_contact, bookmark, book, review, search
            $table->integer('weight')->default(1); // numeric weight for action importance
            $table->string('meta')->nullable(); // category, search term, location, device
            $table->timestamps();

            $table->index(['user_id', 'vendor_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_interactions');
    }
};
