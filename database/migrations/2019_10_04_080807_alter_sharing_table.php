<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\SmsTemplate;

class AlterSharingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('sharing', 'delete_after_expiration')) {
            return;
        }

        Schema::table('sharing', function (Blueprint $table) {
            $table->boolean('delete_after_expiration')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('sharing', 'delete_after_expiration')) {
            return;
        }

        Schema::table('sharing', function ($table) {
            $table->dropColumn('delete_after_expiration');
        });
    }
}
