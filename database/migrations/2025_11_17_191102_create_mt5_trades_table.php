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
        Schema::create('mt5_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mt5_account_id')->constrained()->onDelete('cascade');
            $table->string('ticket')->unique();
            $table->string('symbol');
            $table->enum('type', ['buy', 'sell', 'buy_limit', 'sell_limit', 'buy_stop', 'sell_stop']);
            $table->decimal('volume', 10, 2);
            $table->decimal('open_price', 15, 5);
            $table->decimal('close_price', 15, 5)->nullable();
            $table->decimal('stop_loss', 15, 5)->nullable();
            $table->decimal('take_profit', 15, 5)->nullable();
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('swap', 15, 2)->default(0);
            $table->timestamp('open_time');
            $table->timestamp('close_time')->nullable();
            $table->string('comment')->nullable();
            $table->enum('status', ['open', 'closed', 'pending'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mt5_trades');
    }
};
