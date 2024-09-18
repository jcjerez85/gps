<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MigrateVersion extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('device_icons', 'type'))
        {
            Schema::table('device_icons', function ($table) {
                $table->string('type', 20)->default('icon')->after('id');
            });

            Artisan::call('db:seed', [
                '--class' => 'IconsMigrationVersionSeeder',
                '--force' => true,
            ]);
        }

        if( ! Schema::hasColumn('devices', 'icon_colors'))
        {
            Schema::table('devices', function ($table) {
                $table->string('icon_colors')
                      ->default('{"moving":"green","stopped":"yellow","offline":"red","engine":"yellow"}')
                      ->after('icon_id');
                $table->text('parameters')->after('gprs_templates_only');
            });
        }

        if( ! Schema::hasColumn('users', 'top_toolbar_open'))
        {

            Schema::table('users', function ($table) {
                $table->string('map_controls', 500)->default('{}')->after('week_start_day');
                $table->tinyInteger('top_toolbar_open')->default('1')->after('week_start_day');
            });
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
