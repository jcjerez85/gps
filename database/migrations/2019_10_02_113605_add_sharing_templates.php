<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\SmsTemplate;

class AddSharingTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        EmailTemplate::unguard();

        EmailTemplate::updateOrCreate(['name' => 'sharing_link'], [
            'name'  => 'sharing_link',
            'title' => 'Share link',
            'note'  => 'Hello,<br><br>share link: [link]',
        ]);

        SmsTemplate::unguard();

        SmsTemplate::updateOrCreate(['name' => 'sharing'], [
            'name' => 'sharing_link',
            'title' => 'Share link',
            'note' => 'Hello,\r\n\r\n share link: [link]'
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
