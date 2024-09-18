<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserUsersPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::insert("INSERT INTO `user_permissions` ( `user_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'users' AS `name`, 1 as `view`, 1 as `edit`, 0 as `remove` FROM users WHERE group_id IN (1,3,5,6)");
        DB::insert("INSERT INTO `billing_plan_permissions` ( `plan_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'users' AS `name`, 1 as `view`, 1 as `edit`, 1 as `remove` FROM billing_plans");

        settings('main_settings.user_permissions.users', [
            'view'   => 1,
            'edit'   => 1,
            'remove' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('user_permissions')
            ->where('name', 'users')
            ->delete();

        DB::table('billing_plan_permissions')
            ->where('name', 'users')
            ->delete();
    }
}
