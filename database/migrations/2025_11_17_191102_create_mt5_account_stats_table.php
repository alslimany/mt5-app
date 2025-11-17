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
        Schema::create('mt5_account_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mt5_account_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('balance', 15, 2);
            $table->decimal('equity', 15, 2);
            $table->decimal('profit', 15, 2);
            $table->decimal('drawdown', 15, 2)->default(0);
            $table->decimal('drawdown_percent', 5, 2)->default(0);
            $table->integer('total_trades')->default(0);
            $table->integer('winning_trades')->default(0);
            $table->integer('losing_trades')->default(0);
            $table->decimal('gross_profit', 15, 2)->default(0);
            $table->decimal('gross_loss', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['mt5_account_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mt5_account_stats');
    }
};
