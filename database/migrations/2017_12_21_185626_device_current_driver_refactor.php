<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Tobuli\Entities\Device;

class DeviceCurrentDriverRefactor extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasColumn('devices', 'current_driver_id')) {
            Schema::table('devices', function ($table) {
                $table->integer('current_driver_id')->after('user_id')->unsigned()->nullable()->index();
                $table->foreign('current_driver_id')->references('id')->on('user_drivers')->onDelete('set null');
            });

            $devices = DB::table("user_device_pivot")
                ->addSelect('user_device_pivot.device_id')
                ->addSelect('user_device_pivot.current_driver_id')
                ->join('user_drivers', 'user_drivers.id', '=', 'user_device_pivot.current_driver_id')
                ->whereNotNull('user_device_pivot.current_driver_id')
                ->groupBy('user_device_pivot.device_id')
                ->get()
                ->all();

            foreach ($devices as $item) {
                DB::table("devices")
                    ->where(['id' => $item->device_id])
                    ->update(['current_driver_id' => $item->current_driver_id]);
            }
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
