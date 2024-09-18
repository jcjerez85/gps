<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKindToDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('devices', 'kind')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->unsignedTinyInteger('kind')->default(0)->after('active');
                $table->index(['kind']);
            });
        }

        if (!Schema::hasTable('device_current_beacons_pivot')) {
            Schema::create('device_current_beacons_pivot', function (Blueprint $table) {
                $table->integer('device_id')->unsigned()->index();
                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
                $table->integer('beacon_id')->unsigned()->index();
                $table->foreign('beacon_id')->references('id')->on('devices')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('device_history_beacons_pivot')) {
            Schema::create('device_history_beacons_pivot', function (Blueprint $table) {
                $table->integer('device_id')->unsigned()->index();
                $table->foreign('device_id')->references('id')->on('devices');
                $table->integer('beacon_id')->unsigned()->index();
                $table->foreign('beacon_id')->references('id')->on('devices');
                $table->datetime('date')->nullable();
                $table->unsignedTinyInteger('action');
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
        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex('devices_kind_index');
            $table->dropColumn('kind');
        });

        Schema::dropIfExists('device_current_beacons_pivot');
        Schema::dropIfExists('device_history_beacons_pivot');
    }
}
