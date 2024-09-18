<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTasksForgeinKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('ALTER TABLE `tasks` CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL;');
        DB::statement('ALTER TABLE `tasks` CHANGE `device_id` `device_id` INT(10) UNSIGNED NOT NULL;');

        Schema::table('tasks', function ($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE `task_status` CHANGE `task_id` `task_id` INT(10) UNSIGNED NOT NULL;');
        
        Schema::table('task_status', function ($table) {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}