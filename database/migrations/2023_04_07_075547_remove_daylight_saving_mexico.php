<?php

use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\DeviceFuelMeasurement;

class RemoveDaylightSavingMexico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $country = DB::table("timezones_dst")->where('country', '=', 'Mexico')->first();

        if (empty($country))
            return;

        DB::table("users_dst")->where('country_id', $country->id)->delete();
        DB::table("timezones_dst")->where('id', $country->id)->delete();

        if (settings('main_settings.default_dst_country_id') == $country->id &&
            settings('main_settings.default_dst_type') == 'automatic') {
            settings('main_settings.default_dst_type', 'none');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
