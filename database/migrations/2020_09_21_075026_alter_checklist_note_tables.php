<?php

use Illuminate\Database\Migrations\Migration;

class AlterChecklistNoteTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('checklist', 'notes')) {
            Schema::table('checklist', function ($table) {
                $table->text('notes')
                    ->nullable()
                    ->default(null);
            });
        }

        if (! Schema::hasColumn('checklist_history', 'notes')) {
            Schema::table('checklist_history', function ($table) {
                $table->text('notes')
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
        if (Schema::hasColumn('checklist', 'notes')) {
            Schema::table('checklist', function ($table) {
                $table->dropColumn('notes');
            });
        }

        if (Schema::hasColumn('checklist_history', 'notes')) {
            Schema::table('checklist_history', function ($table) {
                $table->dropColumn('notes');
            });
        }
    }
}
