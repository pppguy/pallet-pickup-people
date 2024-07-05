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
        Schema::create('driver_pickups', function (Blueprint $table) {
            $table->id();
            $table->integer('pickup_status'); // 0 pending, 1 complete, -1 cancelled
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('customer_prompt_id')->constrained('customer_prompts')->onDelete('cascade'); // Reference to customer_prompts table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_pickups');
    }
};
