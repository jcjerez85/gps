<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopupRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('popup_rules')) { return; }

        Schema::create('popup_rules', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('popup_id')->nullable();
            $table->text('rule_name')->nullable();
            $table->text('field_name')->nullable();
            $table->text('field_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('popup_rules');

    }
}
