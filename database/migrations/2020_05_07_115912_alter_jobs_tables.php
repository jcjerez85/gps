<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterJobsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropIndex('jobs_queue_reserved_reserved_at_index');
                $table->dropColumn('reserved');
                $table->index(['queue', 'reserved_at']);
            });
        } catch (Exception $e) {}

        try {
            Schema::table('jobs_failed', function (Blueprint $table) {
                $table->longText('exception')->after('payload');
            });
        } catch (Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->tinyInteger('reserved')->unsigned();
            $table->index(['queue', 'reserved', 'reserved_at']);
            $table->dropIndex('jobs_queue_reserved_at_index');
        });

        Schema::table('jobs_failed', function (Blueprint $table) {
            $table->dropColumn('exception');
        });
    }
}
