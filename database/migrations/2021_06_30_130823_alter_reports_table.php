<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('reports', 'period')) {
            return;
        }

        Schema::table('reports', function ($table) {
            $table->string('period')
                ->nullable()
                ->after('daily_email_sent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('reports', 'period')) {
            return;
        }

        Schema::table('reports', function ($table) {
            $table->dropColumn('period');
        });
    }
}
