<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Eloquent\Relations\Relation;

class UpdateChatParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $morphMap = Relation::morphMap() ?? [];

        foreach ($morphMap as $key => $class) {
            DB::table('chat_participants')
                ->where('chattable_type', $class)
                ->update(['chattable_type' => $key]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $morphMap = Relation::morphMap() ?? [];

        foreach ($morphMap as $key => $class) {
            DB::table('chat_participants')
                ->where('chattable_type', $key)
                ->update(['chattable_type' => $class]);
        }
    }
}
