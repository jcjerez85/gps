<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistTables extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        if (! Schema::hasTable('checklist_template')) {
            Schema::create('checklist_template', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->smallInteger('type');
            });
        }

        if (! Schema::hasTable('checklist_template_row')) {
            Schema::create('checklist_template_row', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('template_id')->unsigned();
                $table->text('activity');

                $table->foreign('template_id')
                    ->references('id')
                    ->on('checklist_template')
                    ->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('checklist')) {
            Schema::create('checklist', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('template_id')->unsigned()->nullable();
                $table->integer('service_id')->unsigned()->nullable();
                $table->string('name');
                $table->smallInteger('type');
                $table->string('signature')->nullable();
                $table->timestamp('time_completed')->nullable();
                $table->timestamps();

                $table->foreign('template_id')
                    ->references('id')
                    ->on('checklist_template')
                    ->onDelete('set NULL');

                $table->foreign('service_id')
                    ->references('id')
                    ->on('device_services')
                    ->onDelete('set NULL');
            });
        }

        if (! Schema::hasTable('checklist_row')) {
            Schema::create('checklist_row', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('checklist_id')->unsigned();
                $table->integer('template_row_id')->unsigned()->nullable();
                $table->text('activity');
                $table->boolean('completed')->default(0);
                $table->text('photo_path')->nullable();
                $table->timestamp('time_completed')->nullable();

                $table->foreign('checklist_id')
                    ->references('id')
                    ->on('checklist')
                    ->onDelete('cascade');

                $table->foreign('template_row_id')
                    ->references('id')
                    ->on('checklist_template_row')
                    ->onDelete('set NULL');
            });
        }

        if (! Schema::hasTable('checklist_history')) {
            Schema::create('checklist_history', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('template_id')->unsigned()->nullable();
                $table->integer('checklist_id')->unsigned()->nullable();
                $table->integer('service_id')->unsigned()->nullable();
                $table->string('name');
                $table->smallInteger('type');
                $table->string('signature')->nullable();
                $table->timestamp('time_completed')->nullable();
                $table->timestamps();

                $table->foreign('template_id')
                    ->references('id')
                    ->on('checklist_template')
                    ->onDelete('set NULL');

                $table->foreign('checklist_id')
                    ->references('id')
                    ->on('checklist')
                    ->onDelete('set NULL');

                $table->foreign('service_id')
                    ->references('id')
                    ->on('device_services')
                    ->onDelete('set NULL');
            });
        }

        if (! Schema::hasTable('checklist_row_history')) {
            Schema::create('checklist_row_history', function(Blueprint $table) {
                $table->integer('checklist_history_id')->unsigned();
                $table->integer('checklist_id')->unsigned()->nullable();
                $table->integer('checklist_row_id')->unsigned()->nullable();
                $table->integer('template_row_id')->unsigned()->nullable();
                $table->text('activity');
                $table->boolean('completed')->default(0);
                $table->text('photo_path')->nullable();
                $table->timestamp('time_completed')->nullable();

                $table->foreign('checklist_history_id')
                    ->references('id')
                    ->on('checklist_history')
                    ->onDelete('cascade');

                $table->foreign('checklist_id')
                    ->references('id')
                    ->on('checklist')
                    ->onDelete('set NULL');

                $table->foreign('checklist_row_id')
                    ->references('id')
                    ->on('checklist_row')
                    ->onDelete('set NULL');

                $table->foreign('template_row_id')
                    ->references('id')
                    ->on('checklist_template_row')
                    ->onDelete('set NULL');
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
        if (Schema::hasTable('checklist_template')) {
            Schema::drop('checklist_template');
        }

        if (Schema::hasTable('checklist_template_row')) {
            Schema::drop('checklist_template_row');
        }

        if (Schema::hasTable('checklist')) {
            Schema::drop('checklist');
        }

        if (Schema::hasTable('checklist_row')) {
            Schema::drop('checklist_row');
        }

        if (Schema::hasTable('checklist_history')) {
            Schema::drop('checklist_history');
        }

        if (Schema::hasTable('checklist_row_history')) {
            Schema::drop('checklist_row_history');
        }
    }
}
