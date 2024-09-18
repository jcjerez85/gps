<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\User;

class AddOpenToRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('routes', 'group_id'))
            return;

        Schema::table('routes', function (Blueprint $table) {
            $table->integer('group_id')
                ->nullable()
                ->default(null)
                ->after('user_id')
                ->unsigned()
                ->index();

            $table->foreign('group_id')->references('id')->on('route_groups')->onDelete('SET NULL');
        });

        DB::statement("UPDATE users SET ungrouped_open = CONCAT(LEFT(ungrouped_open, CHAR_LENGTH(ungrouped_open) - 1), ',\"route_group\":1}')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('routes', 'group_id')) {
            return;
        }

        User::select(['id', 'ungrouped_open'])->chunk(500, function ($users) {
            DB::beginTransaction();

            /** @var User $user */
            foreach ($users as $user) {
                $ungrouped = $user->ungrouped_open;
                unset($ungrouped['route_group']);

                $user->ungrouped_open = $ungrouped;
                $user->update();
            }

            DB::commit();
        });

        Schema::table('routes', function($table) {
            $table->dropColumn('group_id');
        });
    }
}
