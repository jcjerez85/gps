<?php

use Illuminate\Database\Migrations\Migration;

class TransformGeocoderSettings extends Migration
{
    private $geocoderSettings = [
        'geocoder_api'      => 'api',
        'api_key'           => 'api_key',
        'api_url'           => 'api_url',
        'api_app_id'        => 'api_app_id',
        'api_app_secret'    => 'api_app_secret',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $oldSettings = app('Tobuli\Helpers\Settings\SettingsDB')
            ->get('main_settings', false);

        if (empty($oldSettings)) {
            return;
        }

        foreach ($this->geocoderSettings as $currentKey => $newKey) {
            if (isset($oldSettings[$currentKey])) {
                settings('main_settings.geocoders.primary.' . $newKey, $oldSettings[$currentKey]);
            }
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
