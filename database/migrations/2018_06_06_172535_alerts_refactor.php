<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Arr;
use Tobuli\Entities\Alert;

class AlertsRefactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('alerts', 'type'))
            return;

        $alerts = $this->getAlerts();

        Schema::table('alerts', function(Blueprint $table) {
            $table->text('data')->after('name')->nullable();
            $table->text('notifications')->after('name')->nullable();
            $table->text('schedules')->after('name')->nullable();
            $table->string('type', 64)->after('name')->nullable()->index();

            $table->dropColumn('email');
            $table->dropColumn('mobile_phone');
            $table->dropColumn('overspeed_speed');
            $table->dropColumn('overspeed_distance');
            $table->dropColumn('ac_alarm');
            $table->dropColumn('stop_duration');
        });

        Schema::table('alert_device', function(Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('overspeed');
        });

        Schema::table('alert_geofence', function(Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('zone');
            $table->dropColumn('time_from');
            $table->dropColumn('time_to');
        });

        if ( ! Schema::hasTable('alert_zone'))
        {
            Schema::create('alert_zone', function(Blueprint $table)
            {
                $table->integer('alert_id')->unsigned()->index();
                $table->foreign('alert_id')->references('id')->on('alerts')->onDelete('cascade');
                $table->integer('geofence_id')->unsigned()->index();
                $table->foreign('geofence_id')->references('id')->on('geofences')->onDelete('cascade');
            });
        }

        $this->setAlerts($alerts);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }

    private function getAlerts()
    {
        $result = [];

        $alerts = DB::table('alerts')->get()->all();

        foreach ($alerts as $alert) {
            $items = [];

            $_item = [
                'id' => $alert->id,
                'name' => $alert->name,
                'user_id' => $alert->user_id,
                'active' => $alert->active,
                'devices' => Arr::pluck(DB::table('alert_device')->where('alert_id', $alert->id)->get()->all(), 'device_id'),
                'notifications' => [
                    'sound' => ['active' => true],
                    'push'  => ['active' => true],
                ],
                'schedule' => false,
                'created_at' => $alert->created_at,
                'updated_at' => $alert->updated_at,
            ];

            if ($alert->email)
                Arr::set($_item, 'notifications.email', ['active' => true, 'input' => $alert->email]);
            if ($alert->mobile_phone)
                Arr::set($_item, 'notifications.sms', ['active' => true, 'input' => $alert->mobile_phone]);

            if ($alert->overspeed_speed > 0) {
                $items[] = array_merge($_item, [
                    'type' => 'overspeed',
                    'overspeed' => $alert->overspeed_distance == 2 ? milesToKilometers($alert->overspeed_speed) : $alert->overspeed_speed,
                ]);
            }

            if ($alert->stop_duration > 0) {
                $items[] = array_merge($_item, [
                    'type' => 'stop_duration',
                    'stop_duration' => $alert->stop_duration,
                ]);
            }

            $drivers = DB::table('alert_driver_pivot')->where('alert_id', $alert->id)->get()->all();
            if ($drivers) {
                $items[] = array_merge($_item, [
                    'type' => 'driver',
                    'drivers' => Arr::pluck($drivers, 'driver_id'),
                ]);
            }

            $events_custom = DB::table('alert_event_pivot')->where('alert_id', $alert->id)->get()->all();
            if ($events_custom) {
                $items[] = array_merge($_item, [
                    'type' => 'custom',
                    'events_custom' => Arr::pluck($events_custom, 'event_id'),
                ]);
            }

            $geofences = DB::table('alert_geofence')->where('alert_id', $alert->id)->get()->all();
            if ($geofences) {
                $_geofences = [];
                foreach ($geofences as $geofence) {
                    $_geofences[$geofence->time_from . '-' . $geofence->time_to][$geofence->zone][] = $geofence->geofence_id;
                }

                foreach ($_geofences as $time => $values) {
                    if ($time == '00:00-00:00')
                        $_item['schedule'] = false;
                    elseif ($time == '00:00-24:00')
                        $_item['schedule'] = false;
                    else {
                        $_item['schedule'] = true;

                        list($from, $to) = explode('-', $time);

                        $from = $this->roundToQuarterHour($from);
                        $to = $this->roundToQuarterHour($to);

                        $start = \Carbon\Carbon::createMidnightDate()->parse($from);

                        $times = [];

                        for ($i = 1; $i < 100; $i++)
                        {
                            $formated = $start->format('H:i');

                            if (in_array($formated, $times))
                                break;

                            if ($formated == $to)
                                break;

                            $times[] = $formated;

                            $start->addMinutes(15);
                        }

                        $weekdays = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];

                        foreach ($weekdays as $weekday)
                            $_item['schedules'][$weekday] = $times;
                    }

                    $items = array_merge($items, $this->getInOut($_item, $values));
                }
            }

            if (empty($items))
                $items[] = $_item;

            $result[$alert->id] = $items;
        }

        return $result;
    }

    private function getInOut($item, $array)
    {
        $items = [];

        if (isset($array[1]) && isset($array[2])) {
            $in = array_diff($array[1], $array[2]);
            $out = array_diff($array[2], $array[1]);
            $both = array_intersect($array[1], $array[2]);
        } elseif(isset($array[1])) {
            $in = $array[1];
        } elseif(isset($array[2])) {
            $out = $array[2];
        }

        if ( ! empty($in))
            $items[] = array_merge($item, [
                'type' => 'geofence_in',
                'geofences' => $in,
            ]);

        if ( ! empty($out))
            $items[] = array_merge($item, [
                'type' => 'geofence_out',
                'geofences' => $out,
            ]);

        if ( ! empty($both))
            $items[] = array_merge($item, [
                'type' => 'geofence_inout',
                'geofences' => $both,
            ]);

        return $items;
    }

    private function setAlerts($alerts)
    {
        foreach ($alerts as $alert_id => $values)
        {
            if (count($values) > 1) {
                $this->updateAlert( array_shift($values) );
                foreach ($values as $value)
                    $this->createAlert($value);
            } else {
                $this->updateAlert( array_shift($values) );
            }
        }
    }

    private function createAlert($data){
        $prev_alert_id = $data['id'];
        unset($data['id']);

        $alert = Alert::create($data);

        $alert->devices()->sync(Arr::get($data, 'devices', []));
        $alert->geofences()->sync(Arr::get($data, 'geofences', []));
        $alert->drivers()->sync(Arr::get($data, 'drivers', []));
        $alert->zones()->sync(Arr::get($data, 'zones', []));
        $alert->events_custom()->sync(Arr::get($data, 'events_custom', []));

        $types = [$alert->type];

        if ($alert->type == 'geofence_in')
            $types = ['zone_in'];
        if ($alert->type == 'geofence_out')
            $types = ['zone_out'];
        if ($alert->type == 'geofence_inout')
            $types = ['zone_in', 'zone_out'];

        DB::table('events')
            ->where('alert_id', $prev_alert_id)
            ->whereIn('type', $types)
            ->update(['alert_id' => $alert->id]);
    }

    private function updateAlert($data)
    {
        $prev_alert_id = $data['id'];
        unset($data['id']);

        $alert = Alert::find($prev_alert_id);
        $alert->update($data);

        $alert->devices()->sync(Arr::get($data, 'devices', []));
        $alert->geofences()->sync(Arr::get($data, 'geofences', []));
        $alert->drivers()->sync(Arr::get($data, 'drivers', []));
        $alert->zones()->sync(Arr::get($data, 'zones', []));
        $alert->events_custom()->sync(Arr::get($data, 'events_custom', []));
    }

    private function roundToQuarterHour($timestring)
    {
        try {
            $time = \Carbon\Carbon::createMidnightDate()->parse($timestring);
        } catch (Exception $e) {
            $time = \Carbon\Carbon::createMidnightDate();
        }

        $minutes = date('i', strtotime($timestring));

        if ($sub = $minutes % 15)
            $time->subMinutes($sub);

        return $time->format('H:i');
    }
}