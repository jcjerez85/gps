<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAlertDeviceFiredAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('alert_device', 'fired_at')) {
            Schema::table('alert_device', function ($table) {
                $table->dateTime('fired_at')->nullable();
            });

            DB::table('alert_device')
                ->join(DB::raw('(SELECT tmp_events.time, tmp_events.alert_id, tmp_events.device_id
                    FROM (SELECT * FROM events ORDER BY events.id desc) AS tmp_events
                    GROUP BY tmp_events.device_id, tmp_events.alert_id 
                    ) last_event 
                    '),
                    function($join)
                    {
                        $join
                            ->on('alert_device.alert_id', '=', 'last_event.alert_id')
                            ->on('alert_device.device_id', '=', 'last_event.device_id');
                    })
                ->update([
                    'fired_at' => DB::raw("last_event.time")
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alert_device', function($table) {
            $table->dropColumn('fired_at');
        });
    }
}