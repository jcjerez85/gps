<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToDevice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('devices', 'comment')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            $table->text('comment')->after('additional_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('devices', 'comment')) {
            return;
        }

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
}
