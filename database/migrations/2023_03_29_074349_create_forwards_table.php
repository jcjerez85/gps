<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('forwards')) {
            Schema::create('forwards', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->nullable()->index();
                $table->string('type');
                $table->boolean('active')->index();
                $table->boolean('shareable');
                $table->string('title');
                $table->longText('payload')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('user_forward')) {
            Schema::create('user_forward', function (Blueprint $table) {
                $table->integer('user_id')->unsigned()->index();
                $table->integer('forward_id')->unsigned()->index();
                $table->foreign('forward_id')->references('id')->on('forwards')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('device_forward')) {
            Schema::create('device_forward', function (Blueprint $table) {
                $table->integer('device_id')->unsigned()->index();
                $table->integer('forward_id')->unsigned()->index();
                $table->foreign('forward_id')->references('id')->on('forwards')->onDelete('cascade');
                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('device_group_forward')) {
            Schema::create('device_group_forward', function (Blueprint $table) {
                $table->integer('group_id')->unsigned()->index();
                $table->integer('forward_id')->unsigned()->index();
                $table->foreign('forward_id')->references('id')->on('forwards')->onDelete('cascade');
                $table->foreign('group_id')->references('id')->on('device_groups')->onDelete('cascade');
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
        Schema::dropIfExists('user_forward');
        Schema::dropIfExists('device_forward');
        Schema::dropIfExists('device_group_forward');
        Schema::dropIfExists('forwards');
    }
}
