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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable(); // e.g., "Monthly", "Yearly", "One-time"
            $table->integer('amount'); // Amount in cents
            $table->string('currency')->default('usd');
            $table->string('stripe_price_id')->nullable();
            $table->string('type')->default('one_time'); // one_time, recurring
            $table->string('interval')->nullable(); // day, week, month, year
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
