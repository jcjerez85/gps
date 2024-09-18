<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ArrowAckPath extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update('UPDATE device_icons SET path = REPLACE(path, "images/arrow-ack.png", "assets/images/arrow-ack.png") WHERE path = "images/arrow-ack.png"');
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
