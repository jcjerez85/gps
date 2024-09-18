<?php namespace App\Console;

use App\Jobs\RunSchedules;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\Inspire',
        'App\Console\Commands\Tracker\RestartCommand',
        'App\Console\Commands\Tracker\ConfigCommand',
        'App\Console\Commands\Tracker\ConfigAliasCommand',
        'App\Console\Commands\Socket\SSLCertCommand',
        'App\Console\Commands\Socket\ServiceCommand',
        'App\Console\Commands\AutoCleanServerCommand',
        'App\Console\Commands\AutoCleanServerFilterCommand',
        'App\Console\Commands\BackupMysqlCommand',
        'App\Console\Commands\CheckAlertsCommand',
        'App\Console\Commands\CheckServerCommand',
        'App\Console\Commands\CheckServiceCommand',
        'App\Console\Commands\CheckServiceExpireCommand',
        'App\Console\Commands\CheckSchedulesCommand',
        'App\Console\Commands\CleanServerCommand',
        'App\Console\Commands\CleanReportLogCommand',
        'App\Console\Commands\CleanDevicesCommand',
        'App\Console\Commands\CleanEventsCommand',
        'App\Console\Commands\CleanUnregisteredDeviceLogCommand',
        'App\Console\Commands\CleanUserCommand',
        'App\Console\Commands\CleanDeviceCamerasCommand',
        'App\Console\Commands\CleanExpiredSharingsCommand',
        'App\Console\Commands\CleanModelCommand',
        'App\Console\Commands\ReportsDailyCommand',
        'App\Console\Commands\SendEventsCommand',
        'App\Console\Commands\ReportsCleanCommand',
        'App\Console\Commands\ServerTranslationsCommand',
        'App\Console\Commands\OptimizeServerDBCommand',
        'App\Console\Commands\CompressLogsCommand',
        'App\Console\Commands\UpdateIconsCommand',
        'App\Console\Commands\InsertCommand',
        'App\Console\Commands\CheckPositionsCommand',
        'App\Console\Commands\CheckTimeCommand',
        'App\Console\Commands\ResetDevicesTimezoneCommand',
        'App\Console\Commands\CheckSubscriptionsCommand',
        'App\Console\Commands\CheckDevicesExpirationCommand',
        'App\Console\Commands\CheckOfflineDevices',
        'App\Console\Commands\CheckUsersExpirationCommand',
        'App\Console\Commands\SettingsCommand',
        'App\Console\Commands\ArchiveChecklistsCommand',
        'App\Console\Commands\KeyCreateCommand',
        'App\Console\Commands\GeoCacheSetupCommand',
        'App\Console\Commands\CopyDevicesCommand',
        'App\Console\Commands\CalcVirtualOdometerCommand',
        'App\Console\Commands\SplitDevicesCommand',
        'App\Console\Commands\FakeDevicesCommand',
        'App\Console\Commands\DropDeviceFreeTablesCommand',
        'App\Console\Commands\RegenerateDeviceCamerasCommand',
        'App\Console\Commands\AlterPositionTablesCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule
            ->command('queue:restart')
            ->hourly();

        $schedule->command('queue:work redis --sleep=3 --tries=1 --queue=tracker')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule
            ->exec('systemctl restart supervisord')
            ->hourlyAt(3);

        $schedule
            ->command('schedules:check')
            ->everyMinute();

        $schedule
            ->command('subscriptions:check')
            ->hourly();

        $schedule
            ->command('devices_expiration:check')
            ->hourly();

        $schedule
            ->command('users_expiration:check')
            ->hourly();

        $schedule
            ->command('camera:clean')
            ->daily();

        $schedule
            ->command('sharing:clean')
            ->everyMinute();

        $schedule
            ->command('checklists:archive')
            ->withoutOverlapping();

        $schedule
            ->command('devices:check_offline')
            ->everyMinute()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    // protected function commands()
    // {
    //     $this->load(__DIR__.'/Commands');

    //     require base_path('routes/console.php');
    // }
}
