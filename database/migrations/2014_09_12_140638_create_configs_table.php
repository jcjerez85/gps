<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;

class CreateConfigsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::hasTable('configs')) { return; }

		Schema::create('configs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('title');
            $table->text('value');
			$table->timestamps();
		});

        DB::table('configs')->insert(
            array('title' => 'alerts_last_check', 'value' => 0)
        );
		DB::table('configs')->insert(
			array('title' => 'email', 'value' => 'a:0:{}')
		);

		$settings = unserialize('a:12:{s:11:"server_name";s:10:"GPS Server";s:16:"default_language";s:2:"en";s:16:"default_timezone";s:2:"57";s:19:"default_date_format";s:5:"Y-m-d";s:19:"default_time_format";s:5:"H:i:s";s:24:"default_unit_of_distance";s:2:"km";s:24:"default_unit_of_capacity";s:2:"lt";s:24:"default_unit_of_altitude";s:2:"mt";s:11:"default_map";s:1:"2";s:29:"default_object_online_timeout";s:1:"5";s:24:"allow_users_registration";s:1:"0";s:26:"frontpage_logo_padding_top";s:1:"5";}');

		$settings['user_permissions'] = [];
		$permissions = Config::get('tobuli.permissions');
		foreach ($permissions as $key => $val) {
			$settings['user_permissions'][$key] = [
				'view' => $val['view'],
				'edit' => $val['edit'],
				'remove' => $val['remove']
			];
		}

		DB::table('configs')->insert(
			array('title' => 'main_settings', 'value' => serialize($settings))
		);
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('configs');
	}

}
