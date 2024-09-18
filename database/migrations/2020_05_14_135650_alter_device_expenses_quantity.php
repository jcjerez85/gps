<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterDeviceExpensesQuantity extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE device_expenses MODIFY COLUMN quantity decimal(10,2)');
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
