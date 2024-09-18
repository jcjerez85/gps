<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterUserGprsTemplateTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('user_gprs_templates', 'adapted')) {
            return;
        }

        Schema::table('user_gprs_templates', function ($table) {
            $table->string('adapted', 64)
                ->nullable()
                ->default(null)
                ->index()
                ->after('user_id');
        });

        DB::table('user_gprs_templates')
            ->whereNotNull('protocol')
            ->update([
                'adapted' => 'protocol'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('user_gprs_templates', 'adapted')) {
            return;
        }

        Schema::table('user_gprs_templates', function($table) {
            $table->dropColumn('adapted');
        });
    }
}
