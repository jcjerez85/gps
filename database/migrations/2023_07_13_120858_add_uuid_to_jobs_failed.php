<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToJobsFailed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('jobs_failed', 'uuid')) {
            return;
        }

        Schema::table('jobs_failed', function (Blueprint $table) {
            $table->string('uuid')->after('id')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('jobs_failed', 'uuid')) {
            return;
        }

        Schema::table('jobs_failed', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
