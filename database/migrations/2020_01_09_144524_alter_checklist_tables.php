<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Checklist;

class AlterChecklistTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('checklist_images')) {
            Schema::create('checklist_images', function(Blueprint $table) {
                $table->integer('checklist_id')->unsigned()->index();
                $table->integer('row_id')->unsigned()->index();
                $table->integer('checklist_history_id')->unsigned()->nullable();
                $table->string('path');
                $table->timestamps();
            });

            $checklists = Checklist::all();

            foreach ($checklists as $checklist) {
                foreach ($checklist->rows as $row) {
                    if ($row->photo_path) {
                        DB::table('checklist_images')->insert([
                            'checklist_id' => $checklist->id,
                            'row_id' => $row->id,
                            'path' => $row->photo_path,
                        ]);
                    }
                }
            }

            Schema::table('checklist_row', function ($table) {
                $table->dropColumn('photo_path');
            });

            Schema::table('checklist_row_history', function ($table) {
                $table->dropColumn('photo_path');
            });
        }

        if (Schema::hasColumn('checklist', 'time_completed')) {
            DB::statement("ALTER TABLE checklist CHANGE `time_completed` `completed_at` DATETIME NULL;");
        }

        if (Schema::hasColumn('checklist_row', 'time_completed')) {
            DB::statement("ALTER TABLE checklist_row CHANGE `time_completed` `completed_at` DATETIME NULL;");
        }

        if (Schema::hasColumn('checklist_history', 'time_completed')) {
            DB::statement("ALTER TABLE checklist_history CHANGE `time_completed` `completed_at` DATETIME NULL;");
        }

        if (Schema::hasColumn('checklist_row_history', 'time_completed')) {
            DB::statement("ALTER TABLE checklist_row_history CHANGE `time_completed` `completed_at` DATETIME NULL;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
