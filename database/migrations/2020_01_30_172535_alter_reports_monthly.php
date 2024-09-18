<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReportsMonthly extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('reports', 'monthly'))
           return;

        Schema::table('reports', function ($table) {
            $table->boolean('monthly')->nullable()->index();
            $table->string('monthly_time', 5)->default('00:00');
            $table->datetime('monthly_email_sent')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function($table) {
            $table->dropColumn('monthly');
            $table->dropColumn('monthly_time');
            $table->dropColumn('monthly_email_sent');
        });
    }
}