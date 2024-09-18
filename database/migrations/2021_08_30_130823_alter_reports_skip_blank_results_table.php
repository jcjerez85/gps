<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReportsSkipBlankResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('reports', 'skip_blank_results')) {
            return;
        }

        Schema::table('reports', function ($table) {
            $table->boolean('skip_blank_results')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('reports', 'skip_blank_results')) {
            return;
        }

        Schema::table('reports', function ($table) {
            $table->dropColumn('skip_blank_results');
        });
    }
}
