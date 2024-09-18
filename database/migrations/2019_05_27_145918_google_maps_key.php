<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoogleMapsKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $google_api_key = env('google_api_key', null);
        if ( ! $google_api_key)
            return;

        $google_maps_key = settings('main_settings.google_maps_key');
        if ($google_maps_key)
            return;

        settings('main_settings.google_maps_key', $google_api_key);
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
