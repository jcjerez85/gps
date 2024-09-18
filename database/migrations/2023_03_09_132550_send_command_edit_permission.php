<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SendCommandEditPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('user_permissions')
            ->where('name', 'send_command')
            ->where('view', 1)
            ->update(['edit' => 1]);

        DB::table('billing_plan_permissions')
            ->where('name', 'send_command')
            ->where('view', 1)
            ->update(['edit' => 1]);

        $permission = settings('main_settings.user_permissions.send_command');
        if ($permission && $permission['view'])
        {
            $permission['edit'] = 1;

            settings('main_settings.user_permissions.send_command', $permission);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('user_permissions')
            ->where('name', 'send_command')
            ->update(['edit' => 0]);

        DB::table('billing_plan_permissions')
            ->where('name', 'send_command')
            ->update(['edit' => 0]);

        $permission = settings('main_settings.user_permissions.send_command');
        if ($permission && $permission['view'])
        {
            $permission['edit'] = 0;

            settings('main_settings.user_permissions.send_command', $permission);
        }
    }
}
