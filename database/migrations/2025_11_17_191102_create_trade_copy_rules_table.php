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
        Schema::create('trade_copy_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('source_account_id')->constrained('mt5_accounts')->onDelete('cascade');
            $table->foreignId('destination_account_id')->constrained('mt5_accounts')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->decimal('lot_multiplier', 5, 2)->default(1.00);
            $table->boolean('copy_stop_loss')->default(true);
            $table->boolean('copy_take_profit')->default(true);
            $table->json('symbol_filter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_copy_rules');
    }
};
