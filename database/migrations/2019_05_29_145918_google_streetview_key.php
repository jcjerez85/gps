<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GoogleStreetviewKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        settings('main_settings.streetview_api', 'google');

        $google_streetview_api_key = env('streetview_key', null);
        if ( ! $google_streetview_api_key)
            return;

        $streetview_api_key = settings('main_settings.streetview_key');

        if ($streetview_api_key)
            return;

        settings('main_settings.streetview_key', $google_streetview_api_key);
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
