<?php

use Illuminate\Database\Seeder;
use Tobuli\Entities\SmsTemplate;

class SmsTemplatesTableSeeder extends Seeder {

	public function run()
	{
        SmsTemplate::updateOrCreate(['name' => 'event'], [
            'name' => 'event',
            'title' => 'New event',
            'note' => 'Hello,\r\nEvent: [event]\r\nGeofence: [geofence]\r\nDevice: [device.name]\r\nTime: [time]'
        ]);

        SmsTemplate::updateOrCreate(['name' => 'report'], [
            'name' => 'report',
            'title' => 'Report "[name]"',
            'note' => 'Hello,\r\nName: [name]\r\nPeriod: [period]'
        ]);

        SmsTemplate::updateOrCreate(['name' => 'service_expiration'], [
            'name' => 'service_expiration',
            'title' => 'Service expiration',
            'note' => 'Hello, device service is about to expire.\r\n\r\nDevice: [device.name]\r\nService: [service.name]\r\nLeft: [service.left]'
        ]);

        SmsTemplate::updateOrCreate(['name' => 'service_expired'], [
            'name' => 'service_expired',
            'title' => 'Service expired',
            'note' => 'Hello, device service is expired.\r\n\r\nDevice: [device.name]\r\nService: [service.name]'
        ]);

        SmsTemplate::updateOrCreate(['name' => 'sharing_link'], [
            'name' => 'sharing_link',
            'title' => 'Share link',
            'note' => 'Hello,\r\n\r\n share link: [link]'
        ]);
	}

}