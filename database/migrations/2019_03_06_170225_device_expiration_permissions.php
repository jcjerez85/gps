<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeviceExpirationPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::insert("INSERT INTO `user_permissions` ( `user_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'device.expiration_date' AS `name`, 1 as `view`, 1 as `edit`, 0 as `remove` FROM users WHERE group_id IN (1,3)");
        DB::insert("INSERT INTO `user_permissions` ( `user_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'device.expiration_date' AS `name`, 1 as `view`, 0 as `edit`, 0 as `remove` FROM users WHERE group_id = 2");

        DB::insert("INSERT INTO `billing_plan_permissions` ( `plan_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'device.expiration_date' AS `name`, 1 as `view`, 0 as `edit`, 0 as `remove` FROM billing_plans");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
