<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Tobuli\Entities\EmailTemplate;

class EmailTemplatesTableSeeder extends Seeder {

	public function run()
	{
        EmailTemplate::updateOrCreate(['name' => 'event'], [
            'name' => 'event',
            'title' => 'New event',
            'note' => 'Hello,<br><br>Event: [event]<br>Geofence: [geofence]<br>Device: [device.name]<br>Address: [address]<br>Position: [position]<br>Altitude: [altitude]<br>Speed: [speed]<br>Time: [time]'
        ]);

        EmailTemplate::updateOrCreate(['name' => 'email_verification'], [
            'name' => 'email_verification',
            'title' => 'Email verification',
            'note' => 'Verification link: [link]'
        ]);

        EmailTemplate::updateOrCreate(['name' => 'service_expiration'], [
            'name' => 'service_expiration',
            'title' => 'Service expiration',
            'note' => 'Hello, device service is about to expire.<br><br>Device: [device.name]<br>Service: [service.name]<br>Left: [service.left]'
        ]);

        EmailTemplate::updateOrCreate(['name' => 'report'], [
            'name' => 'report',
            'title' => 'Report "[name]"',
            'note' => 'Hello,<br><br>Name: [name]<br>Period: [period]'
        ]);

        EmailTemplate::updateOrCreate(['name' => 'service_expired'], [
            'name' => 'service_expired',
            'title' => 'Service expired',
            'note' => 'Hello, device service is expired.<br><br>Device: [device.name]<br>Service: [service.name]'
        ]);

        EmailTemplate::updateOrCreate(['name' => 'registration'], [
            'name' => 'registration',
            'title' => 'Registration confirmation',
            'note' => 'Hello,<br><br>Thank you for registering, here\'s your login information:<br>Email: [email]<br>Password: [password]'
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expiring_device'], [
            'name'  => 'expiring_device',
            'title' => 'Device expiration',
            'note'  => 'Hello,<br><br>Device ([device.name]) is expiring in [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expired_device'], [
            'name'  => 'expired_device',
            'title' => 'Device expired',
            'note'  => 'Hello,<br><br>Device ([device.name]) expired before [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expiring_user'], [
            'name'  => 'expiring_user',
            'title' => 'User expiration',
            'note'  => 'Hello,<br><br>User ([email]) is expiring in [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expired_user'], [
            'name'  => 'expired_user',
            'title' => 'User expired',
            'note'  => 'Hello,<br><br>User ([email]) expired before [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expiring_sim'], [
            'name'  => 'expiring_sim',
            'title' => 'SIM expiration',
            'note'  => 'Hello,<br><br>Device ([device.name]) SIM ([device.sim_number]) is expiring in [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'expired_sim'], [
            'name'  => 'expired_sim',
            'title' => 'SIM expired',
            'note'  => 'Hello,<br><br>Device ([device.name]) SIM ([device.sim_number]) expired before [days] days',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'sharing_link'], [
            'name'  => 'sharing_link',
            'title' => 'Share link',
            'note'  => 'Hello,<br><br>share link: [link]',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'reset_password'], [
            'name'  => 'reset_password',
            'title' => 'Reset Password',
            'note'  => 'You are receiving this email because we received a password reset request for your account.<br><br>[url]<br><br>If you did not request a password reset, no further action is required.',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'account_created'], [
            'name'  => 'account_created',
            'title' => 'Account created',
            'note' => 'Hello, <br><br> Your account was created. <br><br> Login information: <br> Email: [email] <br> Password: [password]',
        ]);

        EmailTemplate::updateOrCreate(['name' => 'account_password_changed'], [
            'name'  => 'account_password_changed',
            'title' => 'Account password changed',
            'note'  => 'Hello, <br><br> Your password has changed. <br><br> Login information: <br> Email: [email] <br> Password: [password]',
        ]);
	}

}