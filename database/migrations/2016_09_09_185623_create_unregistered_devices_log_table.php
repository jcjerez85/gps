<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateUnregisteredDevicesLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (Schema::connection('traccar_mysql')->hasTable('unregistered_devices_log')) { return; }

		Schema::connection('traccar_mysql')->create('unregistered_devices_log', function(Blueprint $table)
		{
			$table->string('imei', 50)->unique();
			$table->integer('port')->nullable();
			$table->string('ip', 50)->nullable();
			$table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->index();
			$table->integer('times')->unsigned()->default('1');
		});

		DB::connection('traccar_mysql')->statement("
			CREATE TABLE devices (
		  id bigint(20) NOT NULL,
		  name varchar(255) DEFAULT NULL,
		  uniqueId varchar(255) DEFAULT NULL,
		  latestPosition_id bigint(20) DEFAULT NULL,
		  lastValidLatitude double DEFAULT NULL,
		  lastValidLongitude double DEFAULT NULL,
		  other text CHARACTER SET utf8mb4,
		  speed decimal(8,2) DEFAULT NULL,
		  time datetime DEFAULT NULL,
		  server_time datetime DEFAULT NULL,
		  ack_time datetime DEFAULT NULL,
		  altitude double DEFAULT NULL,
		  course double DEFAULT NULL,
		  power double DEFAULT NULL,
		  address varchar(500) CHARACTER SET utf8mb4 DEFAULT NULL,
		  protocol varchar(20) DEFAULT NULL,
		  latest_positions varchar(500) DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

		DB::connection('traccar_mysql')->statement("
			ALTER TABLE devices
		  ADD PRIMARY KEY (id),
		  ADD UNIQUE KEY uniqueId (uniqueId),
		  ADD KEY time (time),
		  ADD KEY FK5CF8ACDD7C6208C3 (latestPosition_id),
		  ADD KEY server_time (server_time),
		  ADD KEY ack_time (ack_time);
		");

		DB::connection('traccar_mysql')->statement("
			ALTER TABLE devices
		  MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
		");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::connection('traccar_mysql')->statement('DROP TABLE unregistered_devices_log');
		DB::connection('traccar_mysql')->statement('DROP TABLE devices');
	}

}
