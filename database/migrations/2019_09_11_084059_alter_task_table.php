<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('tasks', 'invoice_number')) return;
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('invoice_number')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ( ! Schema::hasColumn('tasks', 'invoice_number')) return;

        Schema::table('tasks', function ($table) {
            $table->dropColumn('invoice_number');
        });
    }
}
