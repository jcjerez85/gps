<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UsersTableAlterAvailableMaps extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE  `users` CHANGE  `available_maps` `available_maps` TEXT DEFAULT NULL ;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE  `users` CHANGE  `available_maps` `available_maps` VARCHAR(255) ;');
    }

}
