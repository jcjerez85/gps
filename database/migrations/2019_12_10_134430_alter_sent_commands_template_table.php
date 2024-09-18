<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSentCommandsTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('sent_commands', 'template_id')) return;

        Schema::table('sent_commands', function (Blueprint $table) {
            $table->integer('template_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ( ! Schema::hasColumn('sent_commands', 'template_id')) return;

        Schema::table('sent_commands', function ($table) {
            $table->dropColumn('template_id');
        });
    }
}
