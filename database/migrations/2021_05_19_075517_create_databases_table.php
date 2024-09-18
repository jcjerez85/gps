<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatabasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('databases')) {
            return;
        }

        Schema::create('databases', function(Blueprint $table) {
            $table->increments('id');
            $table->string('driver', 64);
            $table->string('host');
            $table->string('port', 64);
            $table->string('username', 64);
            $table->string('password', 64);
            $table->string('database', 64);
            $table->boolean('active');
            $table->integer('size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('databases')) {
            return;
        }

        Schema::drop('databases');
    }
}
