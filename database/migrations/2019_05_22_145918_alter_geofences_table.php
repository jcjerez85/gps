<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGeofencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasColumn('geofences', 'type'))
            return;

        Schema::table('geofences', function ($table) {
            $table->string('type', '16')->default('polygon')->nullable();
            $table->float('radius')->nullable();
            $table->text('center')->nullable();
        });
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
