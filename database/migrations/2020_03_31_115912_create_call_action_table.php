<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('call_actions')) {
            return;
        }

        Schema::create('call_actions', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id')
                ->unsigned()
                ->nullable();
            $table->integer('user_id')
                ->unsigned()
                ->nullable();
            $table->integer('event_id')
                ->unsigned()
                ->nullable();
            $table->integer('alert_id')
                ->unsigned()
                ->nullable();
            $table->dateTime('called_at')
                ->nullable();
            $table->text('response_type')
                ->nullable();
            $table->text('remarks')
                ->nullable();
            $table->timestamps();

            $table->foreign('device_id')
                ->references('id')
                ->on('devices')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');

            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('SET NULL');

            $table->foreign('alert_id')
                ->references('id')
                ->on('alerts')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('call_actions')) {
            return;
        }

        Schema::drop('call_actions');
    }
}
