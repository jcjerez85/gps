<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceRoutesTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('device_route_types')) {
            return;
        }

        Schema::create('device_route_types', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('device_id')->unsigned()->nullable();
            $table->dateTime('started_at')->nullable()->index();
            $table->dateTime('ended_at')->nullable()->index();
            $table->tinyInteger('type')->nullable()->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('device_route_types')) {
            return;
        }

        Schema::drop('device_route_types');
    }
}
