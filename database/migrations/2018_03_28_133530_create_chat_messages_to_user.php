<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatMessagesToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('chat_messages_to_user')) { return; }

        Schema::create('chat_messages_to_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('message_id');
            $table->dateTime('read_at')->nullable();
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
        Schema::drop('chat_messages_to_user');
    }
}
