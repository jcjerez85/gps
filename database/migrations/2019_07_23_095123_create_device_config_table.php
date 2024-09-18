<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('device_config')) {
            return;
        }

        Schema::create('device_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('brand')->nullable(false);
            $table->string('model')->nullable(false);
            $table->text('commands')->nullable(true);
            $table->boolean('edited')->index()->default(0);
            $table->boolean('active')->index()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('device_config')) {
            return;
        }

        Schema::drop('device_config');
    }
}
