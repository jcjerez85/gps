<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MigrateVersion3Images extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update('UPDATE device_icons SET path = REPLACE(path, "frontend/images/arrow-ack.png", "assets/images/arrow-ack.png")');
        DB::update('UPDATE device_icons SET path = REPLACE(path, "frontend/images/", "images/")');
        DB::update('UPDATE map_icons SET path = REPLACE(path, "frontend/images/", "images/")');
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
