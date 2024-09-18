<?php

use Illuminate\Database\Migrations\Migration;

class AlterGeofencesCoordinates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE geofences MODIFY coordinates MEDIUMTEXT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE geofences MODIFY coordinates TEXT;');
    }
}
