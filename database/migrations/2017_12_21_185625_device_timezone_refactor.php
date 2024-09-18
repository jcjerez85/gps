<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;

use Tobuli\Entities\Device;

class DeviceTimezoneRefactor extends Migration {

    const UTC_TIMEZONE_ID = 57;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('down');

        if ( ! Schema::hasColumn('devices', 'timezone_id'))
        {
            Schema::table('devices', function ($table) {
                $table->integer('timezone_id')->after('user_id')->unsigned()->nullable()->index();
                $table->foreign('timezone_id')->references('id')->on('timezones')->onDelete('set null');
            });

            $this->correctDevicesTimezone();
            $this->correctUsersTimezone();
        }


        if ( ! Schema::connection('traccar_mysql')->hasColumn('devices', 'device_time')) {
            Schema::connection('traccar_mysql')->table('devices', function ($table) {
                $table->datetime('device_time')->after('time')->nullable();
            });

            DB::connection('traccar_mysql')->statement("UPDATE devices SET `device_time` = `time`");
        }


        $devices = \Tobuli\Entities\Device::with('traccar', 'timezone')->get();

        foreach ($devices as $device)
        {
            $table_name = 'positions_'.$device->traccar_device_id;

            if ( ! Schema::connection('traccar_mysql')->hasTable($table_name))
                continue;

            if ( ! Schema::connection('traccar_mysql')->hasColumn($table_name, 'device_time'))
            {
                Schema::connection('traccar_mysql')->table($table_name, function ($table) {
                    $table->datetime('device_time')->after('time')->nullable();
                    $table->text('sensors_values')->after('server_time')->nullable();
                });
            }

            DB::connection('traccar_mysql')->statement("UPDATE $table_name SET `device_time` = `time`");

            if ($device->timezone_id)
                $device->applyPositionsTimezone();
        }

        Artisan::call('up');
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

    private function correctDevicesTimezone()
    {
        $timezones = DB::table("timezones")->get()->all();
        $timezones = Arr::pluck($timezones, 'id');

        $devices = DB::table("user_device_pivot")
            ->addSelect('user_device_pivot.device_id')
            ->addSelect('user_device_pivot.timezone_id AS device_timezone_id')
            ->addSelect('users.timezone_id AS user_timezone_id')
            ->join('users', 'users.id', '=', 'user_device_pivot.user_id')
            ->whereNotNull('user_device_pivot.timezone_id')
            ->where('user_device_pivot.timezone_id', '!=', self::UTC_TIMEZONE_ID)
            ->groupBy('user_device_pivot.device_id')
            ->get()
            ->all();

        foreach ($devices as $item)
        {
            $device = \Tobuli\Entities\Device::with('traccar')->find($item->device_id);

            $device_time = strtotime( $device->getTime() );
            $server_time = strtotime( $device->getServerTime() );
            $ack_time = strtotime( $device->getAckTime() );

            if ($server_time && ($server_time - $device_time < 900))
                continue;

            if ($ack_time && ($ack_time - $device_time < 900))
                continue;


            $sending_timezone_id = $item->user_timezone_id + ($item->user_timezone_id - $item->device_timezone_id);

            $timezone_id = self::UTC_TIMEZONE_ID + (self::UTC_TIMEZONE_ID - $sending_timezone_id);

            if (!in_array($timezone_id, $timezones))
                $timezone_id = null;

            if ($item->user_timezone_id == null || $item->user_timezone_id == self::UTC_TIMEZONE_ID)
                $timezone_id = null;

            if ($timezone_id == self::UTC_TIMEZONE_ID)
                $timezone_id = null;

            if ($timezone_id)
                DB::table("devices")->where(['id' => $item->device_id])->update(['timezone_id' => $timezone_id]);
        }
    }

    private function correctUsersTimezone()
    {
        $usersDevicesTimezones = DB::table("user_device_pivot")
            ->addSelect('user_device_pivot.device_id')
            ->addSelect('user_device_pivot.timezone_id AS device_timezone_id')
            ->addSelect('users.timezone_id AS user_timezone_id')
            ->addSelect('users.id AS user_id')
            ->join('users', 'users.id', '=', 'user_device_pivot.user_id')
            ->whereNotNull('user_device_pivot.timezone_id')
            ->where('users.timezone_id', self::UTC_TIMEZONE_ID)
            ->get()
            ->all();

        $users = [];

        foreach ($usersDevicesTimezones as $timezone)
        {
            if (empty($users[$timezone->user_id]))
                $users[$timezone->user_id] = [];

            $users[$timezone->user_id][] = $timezone->device_timezone_id;
        }

        foreach ($users as $user_id => $userTimezones)
        {
            $timezones = array_count_values($userTimezones);

            arsort($timezones);
            reset($timezones);
            $timezone_id = key($timezones);
            $counts = $timezones[$timezone_id];

            if (empty($counts))
                continue;

            $devices = DB::table("user_device_pivot")->where(['user_id' => $user_id])->count();

            if (empty($devices))
                continue;

            if ($counts / $devices < 0.5)
                continue;

            DB::table("users")->where(['id' => $user_id])->update(['timezone_id' => $timezone_id]);
        }
    }

}
