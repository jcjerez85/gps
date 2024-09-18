<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
        DB::connection('traccar_mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            UsersTableSeeder::class,
            FuelMeasurementsTableSeeder::class,
            DeviceIconsTableSeeder::class,
            MapIconsTableSeeder::class,
            EmailTemplatesTableSeeder::class,
            SmsTemplatesTableSeeder::class,
            TimezonesTableSeeder::class,
            TimezonesDstTableSeeder::class,
        ]);

        DB::connection('traccar_mysql')->statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}
