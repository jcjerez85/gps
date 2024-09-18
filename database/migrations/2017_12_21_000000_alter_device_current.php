<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterDeviceCurrent extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('devices', 'currents'))
            return;

        Schema::table('devices', function ($table) {
            $table->text('currents')->after('parameters')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function($table)
        {
            $table->dropColumn('currents');
        });
    }

}
