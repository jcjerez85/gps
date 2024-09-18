<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\GeofenceGroup;

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

$factory->define(Geofence::class, function (Faker $faker) {
    $data = [
        'name' => $faker->word,
        'active' => 1,
        'polygon_color' => $faker->safeHexColor,
        'speed_limit' => rand(1, 100),
    ];

    if (rand(0, 1)) {
        $data['type'] = Geofence::TYPE_POLYGON;
        $data['polygon'] = [
            ['lat' => $lat = $faker->latitude(-85, 85), 'lng' => $lng = $faker->longitude(-175, 175)],
            ['lat' => $lat + rand(1, 5), 'lng' => $lng + rand(1, 5)],
            ['lat' => $lat - rand(1, 5), 'lng' => $lat - rand(1, 5)],
        ];
    } else {
        $data['type'] = Geofence::TYPE_CIRCLE;
        $data['radius'] = rand(1, 3) * 100000;
        $data['center'] = ['lat' => $faker->latitude, 'lng' => $faker->longitude];
    }

    return $data;
})->afterCreating(GeofenceGroup::class, function (GeofenceGroup $group, Faker $faker) {
    for ($i = 0, $iMax = rand(1, 3); $i < $iMax; $i++) {
        factory(Geofence::class)->create([
            'group_id' => $group->id,
            'user_id' => $group->user_id,
        ]);
    }
});