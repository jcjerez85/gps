<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSentCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('sent_commands', 'response')) return;

        Schema::table('sent_commands', function (Blueprint $table) {
            $table->morphs('actor');
            $table->text('response')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ( ! Schema::hasColumn('sent_commands', 'response')) return;

        Schema::table('sent_commands', function ($table) {
            $table->dropColumn('actor_id');
            $table->dropColumn('actor_type');
            $table->dropColumn('response');
        });
    }
}
