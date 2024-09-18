<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuthenticationToDevice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('devices', 'authentication')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            $table->string('authentication')->after('additional_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('devices', 'authentication')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('authentication');
        });
    }
}
