<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PropertyPermissionsMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('user_permissions')->where('name', 'forward')->update(['name' => 'device.forward']);
        DB::table('user_permissions')->where('name', 'protocol')->update(['name' => 'device.protocol']);

        DB::insert("INSERT INTO `user_permissions` ( `user_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'device.imei' AS `name`, 1 as `view`, 1 as `edit`, 0 as `remove` FROM users");
        DB::insert("INSERT INTO `user_permissions` ( `user_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'device.sim_number' AS `name`, 1 as `view`, 1 as `edit`, 0 as `remove` FROM users");
        DB::insert("INSERT INTO `user_permissions` ( `user_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'device.forward' AS `name`, 0 as `view`, 0 as `edit`, 0 as `remove` FROM users LEFT JOIN `user_permissions` ON (user_permissions.user_id = users.id AND user_permissions.name = 'device.forward') WHERE user_permissions.user_id IS NULL");

        DB::table('billing_plan_permissions')->where('name', 'forward')->update(['name' => 'device.forward']);
        DB::table('billing_plan_permissions')->where('name', 'protocol')->update(['name' => 'device.protocol']);

        DB::insert("INSERT INTO `billing_plan_permissions` ( `plan_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'device.imei' AS `name`, 1 as `view`, 1 as `edit`, 0 as `remove` FROM billing_plans");
        DB::insert("INSERT INTO `billing_plan_permissions` ( `plan_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'device.sim_number' AS `name`, 1 as `view`, 1 as `edit`, 0 as `remove` FROM billing_plans");
        DB::insert("INSERT INTO `billing_plan_permissions` ( `plan_id`, `name`, `view`, `edit`, `remove` ) SELECT id, 'device.forward' AS `name`, 0 as `view`, 0 as `edit`, 0 as `remove` FROM billing_plans LEFT JOIN `billing_plan_permissions` ON (billing_plan_permissions.plan_id = billing_plans.id AND billing_plan_permissions.name = 'device.forward') WHERE billing_plan_permissions.plan_id IS NULL");
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
