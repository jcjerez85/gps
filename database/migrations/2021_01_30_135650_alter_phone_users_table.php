<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterPhoneUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'phone_number')) {
            return;
        }

        Schema::table('users', function ($table) {
            $table->string('phone_number')
                ->nullable()
                ->default(null)
                ->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('users', 'phone_number')) {
            return;
        }

        Schema::table('users', function($table) {
            $table->dropColumn('phone_number');
        });
    }
}
