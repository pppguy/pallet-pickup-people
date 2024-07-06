<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['driver_pickups', 'customer_prompts'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'created_at') && Schema::hasColumn($table->getTable(), 'updated_at')) {
                    $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
                    $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = ['driver_pickups', 'customer_prompts'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'created_at') && Schema::hasColumn($table->getTable(), 'updated_at')) {
                    $table->timestamp('created_at')->nullable()->default(null)->change();
                    $table->timestamp('updated_at')->nullable()->default(null)->change();
                }
            });
        }
    }
};
