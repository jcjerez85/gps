<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoginTokenToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'login_token')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('login_token')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('users', 'login_token')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login_token');
        });
    }
}
