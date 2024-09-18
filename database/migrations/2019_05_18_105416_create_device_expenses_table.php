<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('device_expenses')) { return; }

        Schema::create('device_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->integer('device_id')->unsigned()->index();
            $table->integer('type_id')->unsigned()->index()->nullable();
            $table->string('name')->nullable(false);
            $table->integer('quantity')->nullable(false);
            $table->float('unit_cost')->nullable(false);
            $table->string('supplier')->nullable(true);
            $table->string('buyer')->nullable(true);
            $table->text('additional')->nullable(true);
            $table->timestamp('date')->nullable(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('device_expense_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('device_expenses');
    }
}
