<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\User;

class AlterUsersAddEmailVerified extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'email_verified_at')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('email_verified_at')->nullable();
        });

        User::query()->update(['email_verified_at' => date('Y-m-d H:i:s')]);

        EmailTemplate::unguard();

        EmailTemplate::updateOrCreate(['name' => 'email_verification'], [
            'name' => 'email_verification',
            'title' => 'Email verification',
            'note' => 'Verification link: [link]'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
        });
    }
}
