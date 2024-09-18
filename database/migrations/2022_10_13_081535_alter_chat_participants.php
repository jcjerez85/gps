<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterChatParticipants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('chat_participants', 'read_at')) {
            Schema::table('chat_participants', function (Blueprint $table) {
                $table->timestamp('read_at');
            });

            $now = date('Y-m-d H:i:s');

            DB::update("UPDATE chat_participants SET read_at = '$now'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('chat_participants', 'read_at')) {
            Schema::table('chat_participants', function (Blueprint $table) {
                $table->dropColumn('read_at');
            });
        }
    }
}
