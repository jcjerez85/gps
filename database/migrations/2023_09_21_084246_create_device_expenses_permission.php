<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceExpensesPermission extends Migration
{
    private const KEY_NAME = 'device_expenses';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new \Tobuli\Services\PermissionService())->addToAll(self::KEY_NAME);
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
