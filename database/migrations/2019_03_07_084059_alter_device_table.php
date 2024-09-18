<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('devices', 'installation_date')) return;
        
        Schema::table('devices', function (Blueprint $table) {
            $table->date('installation_date')->after('expiration_date')->default('0000-00-00');
            $table->date('sim_activation_date')->after('expiration_date')->default('0000-00-00');
            $table->date('sim_expiration_date')->after('expiration_date')->default('0000-00-00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ( ! Schema::hasColumn('devices', 'installation_date')) return;

        Schema::table('devices', function ($table) {
            $table->dropColumn('installation_date');
            $table->dropColumn('sim_activation_date');
            $table->dropColumn('sim_expiration_date');
        });
    }
}
