<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterChecklistRowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('checklist_row', 'outcome')) {
            Schema::table('checklist_row', function ($table) {
                $table->string('outcome')
                    ->nullable()
                    ->default(null);
            });
        }

        if (! Schema::hasColumn('checklist_row_history', 'outcome')) {
            Schema::table('checklist_row_history', function ($table) {
                $table->string('outcome')
                    ->nullable()
                    ->default(null);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('checklist_row', 'outcome')) {
            Schema::table('checklist_row', function ($table) {
                $table->dropColumn('outcome');
            });
        }

        if (Schema::hasColumn('checklist_row_history', 'outcome')) {
            Schema::table('checklist_row_history', function ($table) {
                $table->dropColumn('outcome');
            });
        }
    }
}
