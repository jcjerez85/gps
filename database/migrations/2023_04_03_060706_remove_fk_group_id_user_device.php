<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFkGroupIdUserDevice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('user_device_pivot', function(Blueprint $table) {
                $table->dropForeign(['group_id']);
            });
        } catch (Exception $e) {}

        DB::statement('UPDATE user_device_pivot SET group_id = 0 WHERE group_id IS NULL');
        DB::statement('ALTER TABLE user_device_pivot ALTER group_id SET DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE user_device_pivot ALTER group_id DROP DEFAULT');
        DB::statement('UPDATE user_device_pivot p SET group_id = NULL WHERE NOT EXISTS(
            SELECT id FROM device_groups WHERE id = p.group_id
        )');

        Schema::table('user_device_pivot', function(Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('device_groups')->onDelete('set null');
        });
    }
}
