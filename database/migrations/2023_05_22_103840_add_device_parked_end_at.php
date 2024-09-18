<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddDeviceParkedEndAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::connection('traccar_mysql')->hasColumn('devices', 'parked_end_at')) {
            return;
        }

        Schema::connection('traccar_mysql')->table('devices', function ($table) {
            $table->dateTime('parked_end_at')->after('stoped_at')->nullable()->default(null);
            $table->dateTime('stop_begin_at')->after('stoped_at')->nullable()->default(null);
            $table->dateTime('move_begin_at')->after('stoped_at')->nullable()->default(null);
        });

        DB::connection('traccar_mysql')->statement("UPDATE devices SET `stop_begin_at` = `stoped_at`");
        DB::connection('traccar_mysql')->statement("UPDATE devices SET `move_begin_at` = `moved_at`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::connection('traccar_mysql')->hasColumn('devices', 'parked_end_at')) {
            return;
        }

        Schema::connection('traccar_mysql')->table('devices', function ($table) {
            $table->dropColumn('parked_end_at');
            $table->dropColumn('stop_begin_at');
            $table->dropColumn('move_begin_at');
        });
    }
}
