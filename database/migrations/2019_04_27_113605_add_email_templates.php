<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\EmailTemplate;

class AddEmailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        EmailTemplate::unguard();

        EmailTemplate::updateOrCreate(['name' => 'expiring_device'], [
            'name'  => 'expiring_device',
            'title' => 'Device expiration',
            'note'  => 'Hello,<br><br>Device ([device.name]) is expiring in [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expired_device'], [
            'name'  => 'expired_device',
            'title' => 'Device expired',
            'note'  => 'Hello,<br><br>Device ([device.name]) expired before [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expiring_user'], [
            'name'  => 'expiring_user',
            'title' => 'User expiration',
            'note'  => 'Hello,<br><br>User ([email]) is expiring in [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expired_user'], [
            'name'  => 'expired_user',
            'title' => 'User expired',
            'note'  => 'Hello,<br><br>User ([email]) expired before [days] days',
        ]);
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
