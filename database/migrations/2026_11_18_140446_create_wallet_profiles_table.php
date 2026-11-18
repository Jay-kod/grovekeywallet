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
        Schema::create('wallet_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('address', 42);
            $table->string('network_name', 32)->default('Sepolia');
            $table->unsignedBigInteger('chain_id')->default(11155111);
            $table->string('label')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'address']);
            $table->index('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_profiles');
    }
};
