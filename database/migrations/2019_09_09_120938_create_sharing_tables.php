<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sharing')) { 
            Schema::create('sharing', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->index();
                $table->string('name')->nullable();
                $table->string('hash', 32)->index();
                $table->dateTime('expiration_date')->nullable();
                $table->boolean('active')->nullable()->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('sharing_device_pivot')) { 
            Schema::create('sharing_device_pivot', function (Blueprint $table) {
                $table->integer('sharing_id')->unsigned()->index();
                $table->integer('device_id')->unsigned()->index();
                $table->integer('user_id')->unsigned()->index();
                $table->dateTime('expiration_date')->nullable();
                $table->boolean('active')->nullable()->default(1);

                $table->foreign('sharing_id')->references('id')->on('sharing')->onDelete('cascade');
                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('sharing');
        Schema::dropIfExists('sharing_device_pivot');
    }
}
