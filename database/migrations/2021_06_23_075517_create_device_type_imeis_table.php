<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTypeImeisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('device_type_imeis')) {
            return;
        }

        Schema::create('device_type_imeis', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('device_type_id')->unsigned()->nullable();
            $table->string('imei');
            $table->timestamps();

            $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('device_type_imeis')) {
            return;
        }

        Schema::drop('device_type_imeis');
    }
}
