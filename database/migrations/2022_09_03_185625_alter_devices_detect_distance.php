<?php

use Illuminate\Database\Migrations\Migration;

class AlterDevicesDetectDistance extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('devices', 'detect_distance')) {
            Schema::table('devices', function ($table) {
                $table->string('detect_distance', 30)->after('detect_engine')->nullable();
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
        Schema::table('devices', function($table) {
            $table->dropColumn('detect_distance');
        });
    }

}
