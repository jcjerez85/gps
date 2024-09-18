<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\EmailTemplate;


class AddAccountCreatedTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        EmailTemplate::unguard();

        if (Schema::hasTable('email_templates')) {
            EmailTemplate::updateOrCreate(['name' => 'account_created'], [
                'name' => 'account_created',
                'title' => 'Account created',
                'note' => 'Hello, <br><br> Your account was created. <br><br> Login information: <br> Email: [email] <br> Password: [password]',
            ]);
        }
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
