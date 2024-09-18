<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Tobuli\Entities\GeofenceGroup;
use Tobuli\Entities\User;

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

$factory->define(GeofenceGroup::class, function (Faker $faker) {
    return [
        'title' => $faker->domainWord,
        // todo: after migration set 'open'
    ];
})->afterCreating(User::class, function (User $user, Faker $faker) {
    $openGroups = [0];

    for ($i = 0, $iMax = rand(1, 3); $i < $iMax; $i++) {
        $group = factory(GeofenceGroup::class)->create(['user_id' => $user->id]);

        $openGroups[] = $group->id;
    }

    $user->open_geofence_groups = json_encode($openGroups); // todo: remove after 'open' is migrated
    $user->save();
});