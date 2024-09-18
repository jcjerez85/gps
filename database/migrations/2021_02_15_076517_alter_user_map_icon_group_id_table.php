<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserMapIconGroupIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('user_map_icons', 'group_id'))
            return;

        Schema::table('user_map_icons', function ($table) {
            $table->integer('group_id')
                ->nullable()
                ->default(null)
                ->after('user_id')
                ->unsigned()
                ->index();

            $table->foreign('group_id')->references('id')->on('poi_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('user_map_icons', 'group_id')) {
            return;
        }

        Schema::table('user_map_icons', function($table) {
            $table->dropColumn('group_id');
        });
    }
}
