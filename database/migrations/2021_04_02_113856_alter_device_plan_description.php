<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDevicePlanDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('device_plans', 'description'))
            return;

        Schema::table('device_plans', function(Blueprint $table) {
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('device_plans', 'description')) {
            return;
        }

        Schema::table('device_plans', function($table) {
            $table->dropColumn('description');
        });
    }
}
