<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdatedAtDeviceTraccar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::connection('traccar_mysql')->hasColumn('devices', 'updated_at')) {
            return;
        }

        Schema::connection('traccar_mysql')->table('devices', function ($table) {
            $table->dateTime('updated_at')->nullable()->after('engine_changed_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::connection('traccar_mysql')->hasColumn('devices', 'updated_at')) {
            return;
        }

        Schema::connection('traccar_mysql')->table('devices', function(Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
}
