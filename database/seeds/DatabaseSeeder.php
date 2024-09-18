<?php

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
		$this->call('UsersTableSeeder');
        $this->call('FuelMeasurementsTableSeeder');
        $this->call('DeviceIconsTableSeeder');
        $this->call('MapIconsTableSeeder');
        $this->call('EmailTemplatesTableSeeder');
        $this->call('SmsTemplatesTableSeeder');
        $this->call('TimezonesTableSeeder');
        $this->call('TimezonesDstTableSeeder');
        DB::connection('traccar_mysql')->statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}
