<?php namespace App\Console\Commands;

use Formatter;
use Illuminate\Console\Command;
use Tobuli\Entities\DeviceService;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\SmsTemplate;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag as Bugsnag;

class CheckServiceCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'service:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check service expiration command.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $services = DeviceService::expireByDays('device_services.remind_date')
            ->notSend()
            ->notExpired()
            ->get();

        foreach ($services as $service) {
            $this->sendEventInfo($service);
        }

        $services = DeviceService::expireByOdometer('device_services.remind')
            ->notSend()
            ->notExpired()
            ->get();

        foreach ($services as $service) {
            $this->sendEventInfo($service);
        }


        $services = DeviceService::expireByEngineHours('device_services.remind')
            ->notSend()
            ->notExpired()
            ->get();

        foreach ($services as $service) {
            $this->sendEventInfo($service);
        }

        return 'DONE';
    }

    private function sendEventInfo($service)
    {
        if ($service->user)
            Formatter::byUser($service->user);
        else
            Formatter::byDefault();

        $emailTemplate = EmailTemplate::getTemplate('service_expiration', $service->user);

        try {
            sendTemplateEmail($service->email, $emailTemplate, $service);
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
        }

        $smsTemplate = SmsTemplate::getTemplate('service_expiration', $service->user);

        try {
            sendTemplateSMS($service->mobile_phone, $smsTemplate, $service, $service->user_id);
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
        }

        $service->update(['event_sent' => 1]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }
}
