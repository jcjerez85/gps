<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApnConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('apn_config')) {
            return;
        }

        Schema::create('apn_config', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->string('apn_name')->nullable(false);
            $table->string('apn_username')->nullable(true);
            $table->string('apn_password')->nullable(true);
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
        if (! Schema::hasTable('apn_config')) {
            return;
        }

        Schema::drop('apn_config');
    }
}
