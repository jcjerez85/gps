<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterChecklistTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('checklist_template', 'user_id')) {
            DB::table('checklist_template')
                ->delete();

            Schema::table('checklist_template', function ($table) {
                $table->integer('user_id')
                    ->unsigned()
                    ->index()
                    ->after('id');

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
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
        if (Schema::hasColumn('checklist_template', 'user_id')) {
            Schema::table('checklist_template', function ($table) {
                $table->dropColumn('user_id');
            });
        }
    }
}
