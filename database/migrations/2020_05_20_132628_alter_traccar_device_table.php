<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTraccarDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::connection('traccar_mysql')->hasColumn('devices', 'engine_changed_at')) {
            return;
        }

        Schema::connection('traccar_mysql')->table('devices', function ($table) {
            $table->dateTime('engine_changed_at')
                ->nullable()
                ->default(null)
                ->after('engine_off_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::connection('traccar_mysql')->hasColumn('devices', 'engine_changed_at')) {
            return;
        }

        Schema::connection('traccar_mysql')->table('devices', function($table) {
            $table->dropColumn('engine_changed_at');
        });
    }
}
