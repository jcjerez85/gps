<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceGroup;
use Tobuli\Services\DeviceService;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$defaults = \Illuminate\Support\Arr::except(
    app()->make(DeviceService::class)->getDefaults(),
    ['group_id', 'device_icons_type']
);

$factory->define(Device::class, function (Faker $faker) use ($defaults) {
    return [
        'name' => $faker->domainWord(),
        'plate_number' => $faker->bothify(),
        'imei' => $faker->imei,
    ] + $defaults;
})->afterCreating(DeviceGroup::class, function (DeviceGroup $group, Faker $faker) {
    for ($i = 0, $iMax = rand(1, 3); $i < $iMax; $i++) {
        $device = factory(Device::class)->create([
            'user_id' => $group->user_id,
        ]);
        $device->users()->save($group->user);
    }
})->afterCreating(Device::class, function (Device $device, Faker $faker) {
    $time = date('Y-m-d H:i:s');

    $device->traccar->protocol = 'demo';
    $device->traccar->lastValidLatitude = $faker->latitude;
    $device->traccar->lastValidLongitude = $faker->longitude;
    $device->traccar->device_time = $time;
    $device->traccar->time = $time;
    $device->traccar->server_time = $time;

    if ($device->speed > $device->min_moving_speed) {
        $device->traccar->moved_at = $time;
    } else {
        $device->traccar->stoped_at = $time;
    }

    $device->save();
});