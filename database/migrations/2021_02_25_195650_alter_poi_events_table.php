<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterPoiEventsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('events', 'poi_id')) {
            return;
        }

        $this->cleanEvents();

        Schema::table('events', function ($table) {
            $table->integer('poi_id')->unsigned()->nullable()->after('geofence_id');
            $table->foreign('poi_id')->references('id')->on('user_map_icons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('events', 'poi_id')) {
            return;
        }

        Schema::table('events', function($table) {
            $table->dropColumn('poi_id');
        });
    }

    protected function cleanEvents()
    {
        $settings = settings('db_clear');
        $date = Carbon::now()->subDays($settings['days']);

        Artisan::call('events:clean', ['date' => $date]);
    }
}
