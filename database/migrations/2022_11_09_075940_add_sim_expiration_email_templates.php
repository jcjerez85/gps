<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\EmailTemplate;

class AddSimExpirationEmailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        EmailTemplate::unguard();

        EmailTemplate::updateOrCreate(['name' => 'expiring_sim'], [
            'name'  => 'expiring_sim',
            'title' => 'SIM expiration',
            'note'  => 'Hello,<br><br>Device ([device.name]) SIM ([device.sim_number]) is expiring in [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expired_sim'], [
            'name'  => 'expired_sim',
            'title' => 'SIM expired',
            'note'  => 'Hello,<br><br>Device ([device.name]) SIM ([device.sim_number]) expired before [days] days',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function down()
    {
        EmailTemplate::whereIn('name', ['expiring_sim', 'expired_sim'])->delete();
    }
}
