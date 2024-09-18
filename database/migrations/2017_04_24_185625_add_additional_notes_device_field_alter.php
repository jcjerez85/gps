<?php

use Illuminate\Database\Migrations\Migration;

class AddAdditionalNotesDeviceFieldAlter extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('devices', 'additional_notes')) {
            Schema::table('devices', function ($table) {
                $table->string('additional_notes')->after('object_owner')->nullable();
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
        Schema::table('devices', function($table) {
            $table->dropColumn('additional_notes');
        });
    }

}
