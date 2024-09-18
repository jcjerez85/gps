<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\EmailTemplate;

class AddPasswordChangedTemplate extends Migration
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
            EmailTemplate::updateOrCreate(['name' => 'account_password_changed'], [
                'name'  => 'account_password_changed',
                'title' => 'Account password changed',
                'note'  => 'Hello, <br><br> Your password has changed. <br><br> Login information: <br> Email: [email] <br> Password: [password]',
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
