<?php namespace App\Console\Commands;

use App\Services\Mail\PostmarkTransport;
use Formatter;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Tobuli\Entities\DeviceService;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\SmsTemplate;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag as Bugsnag;


class CheckServiceExpireCommand extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'service:check_expire';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Check for service expired.';

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
        $services = DeviceService::expireByDays('device_services.expires_date')
            ->notExpired()
            ->get();

        foreach ($services as $service)
            $this->processItem($service);


        $services = DeviceService::expireByOdometer('device_services.expires')
            ->notExpired()
            ->get();

        foreach ($services as $service)
            $this->processItem($service);


        $services = DeviceService::expireByEngineHours('device_services.expires')
            ->notExpired()
            ->get();

        foreach ($services as $service)
            $this->processItem($service);


        return 'DONE';
    }

    protected function processItem($service)
    {
        if ($service->user)
            Formatter::byUser($service->user);
        else
            Formatter::byDefault();

        $emailTemplate = EmailTemplate::getTemplate('service_expired', $service->user);

        try {
            sendTemplateEmail($service->email, $emailTemplate, $service);
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
        }

        $smsTemplate = SmsTemplate::getTemplate('service_expired', $service->user);

        try {
            sendTemplateSMS($service->mobile_phone, $smsTemplate, $service, $service->user_id);
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
        }

        if ( ! $service->renew_after_expiration)
        {
            $service->update([
                'expired' => 1
            ]);

            return;
        }

        switch ($service->expiration_by) {
            case 'odometer':
                $sensor = $service->device->getOdometerSensor();
                $values = $sensor ? ['odometer' => $sensor->getValueCurrent($service->device->other)] : [];
                break;

            case 'engine_hours':
                $sensor = $service->device->getEngineHoursSensor();
                $values = $sensor ? ['engine_hours' => $sensor->getValueCurrent($service->device->other)] : [];
                break;

            default:
                $values = [];
        }

        $update = prepareServiceData($service->toArray(), $values);
        $update = Arr::only($update, $service->getFillable());

        $service->update($update);

        return null;
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
