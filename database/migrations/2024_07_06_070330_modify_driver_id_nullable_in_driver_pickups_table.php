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
        Schema::table('driver_pickups', function (Blueprint $table) {
            // Modify the driver_id field to be nullable
            $table->foreignId('driver_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_pickups', function (Blueprint $table) {
            // Revert the driver_id field to be non-nullable
            $table->foreignId('driver_id')->nullable(false)->change();
        });
    }
};
