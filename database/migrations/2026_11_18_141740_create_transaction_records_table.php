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
        Schema::create('transaction_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_profile_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_hash', 66)->nullable()->unique();
            $table->string('from_address', 42);
            $table->string('to_address', 42);
            $table->string('amount_wei', 78);
            $table->string('network_name', 32)->default('Sepolia');
            $table->unsignedBigInteger('chain_id')->default(11155111);
            $table->string('gas_limit')->nullable();
            $table->string('gas_price_wei')->nullable();
            $table->string('status', 16)->default('awaiting_approval');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_records');
    }
};
