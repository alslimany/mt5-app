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
        Schema::table('mt5_accounts', function (Blueprint $table) {
            $table->string('password')->after('account_number');
            $table->string('investor_password')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mt5_accounts', function (Blueprint $table) {
            $table->dropColumn(['password', 'investor_password']);
        });
    }
};
