<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (Schema::hasTable('chat_participants')) { return; }

        Schema::create('chat_participants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chat_id');
//            $table->foreign('chat_id')->references('id')->on('chat');
            $table->morphs('chattable');
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
        Schema::drop('chat_participants');
    }
}
