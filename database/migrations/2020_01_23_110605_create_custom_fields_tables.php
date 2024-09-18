<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomFieldsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('custom_fields')) {
            Schema::create('custom_fields', function(Blueprint $table) {
                $table->increments('id');
                $table->string('model')->nullable();
                $table->string('title')->nullable();
                $table->string('data_type')->nullable();
                $table->text('options')->nullable();
                $table->string('default')->nullable();
                $table->string('slug')->nullable();
                $table->boolean('required')->nullable();
                $table->string('validation')->nullable();
            });
        }

        if (! Schema::hasTable('custom_values')) {
            Schema::create('custom_values', function(Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('custom_field_id')->unsigned();
                $table->integer('customizable_id')->unsigned();
                $table->string('customizable_type')->nullable();
                $table->string('value')->nullable();
                $table->timestamps();

                $table->foreign('custom_field_id')
                    ->references('id')
                    ->on('custom_fields')
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
        //
    }
}
