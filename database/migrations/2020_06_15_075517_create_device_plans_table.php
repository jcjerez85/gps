<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('device_plans')) {
            return;
        }

        Schema::create('device_plans', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->decimal('price', 8, 2);
            $table->string('duration_type');
            $table->integer('duration_value');
            $table->boolean('active');
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
        if (! Schema::hasTable('device_plans')) {
            return;
        }

        Schema::drop('device_plans');
    }
}
