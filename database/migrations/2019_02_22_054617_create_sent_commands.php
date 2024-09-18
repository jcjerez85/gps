<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentCommands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('sent_commands')) { return; }

        Schema::create('sent_commands', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('template_id')->unsigned()->nullable();
            $table->string('device_imei')->index();
            $table->morphs('actor');
            $table->string('connection');
            $table->string('command');
            $table->text('parameters')->nullable(true);
            $table->text('response')->nullable(true);
            $table->tinyInteger('status');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_imei')->references('imei')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sent_commands');
    }
}
