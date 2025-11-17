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
        Schema::create('mt5_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('account_name');
            $table->string('account_number')->unique();
            $table->string('server');
            $table->string('broker');
            $table->string('api_token')->nullable();
            $table->string('api_secret')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('equity', 15, 2)->default(0);
            $table->decimal('margin', 15, 2)->default(0);
            $table->decimal('free_margin', 15, 2)->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->integer('leverage')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mt5_accounts');
    }
};
