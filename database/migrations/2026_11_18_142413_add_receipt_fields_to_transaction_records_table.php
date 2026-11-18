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
        Schema::table('transaction_records', function (Blueprint $table) {
            $table->string('gas_used')->nullable()->after('gas_limit');
            $table->string('block_number')->nullable()->after('gas_used');
            $table->timestamp('confirmed_at')->nullable()->after('submitted_at');
            $table->timestamp('last_checked_at')->nullable()->after('confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_records', function (Blueprint $table) {
            $table->dropColumn(['gas_used', 'block_number', 'confirmed_at', 'last_checked_at']);
        });
    }
};
