<?php namespace App\Console\Commands;
set_time_limit(0);

use Formatter;
use CustomFacades\Server;
use App\Console\ProcessManager;
use Illuminate\Console\Command;
use Tobuli\Helpers\ReportHelper;
use Tobuli\Reports\ReportManager;
use Tobuli\Entities\EmailTemplate;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Repositories\ReportLogRepo;
use Symfony\Component\Console\Input\InputArgument;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag as Bugsnag;
use Tobuli\Repositories\Report\ReportRepositoryInterface as Report;

class ReportsDailyCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'reports:daily';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';
    /**
     * @var Report
     */
    private $report;

    private $users = [];

    private $processManager;

    /**
     * @var ReportManager
     */
    private $reportManager;

    /**
     * Create a new command instance.
     *

     */
	public function __construct(Report $report)
	{
		parent::__construct();
        $this->report = $report;

        $this->reportManager = new ReportManager();

        Server::setMemoryLimit(config('server.report_memory_limit'));
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        $timeout = config('tobuli.process.reportdaily_timeout');
        $limit = config('tobuli.process.reportdaily_limit');

        $this->processManager = new ProcessManager('reports:daily', $timeout, $limit);

        if ( ! $this->processManager->canProcess())
        {
            echo "Cant process \n";
            return -1;
        }

        @mkdir(storage_path('cache'));
        @chmod(storage_path('cache'), 0777);

        $schedule_type = $this->argument('type');

        if ($schedule_type == 'daily')
            $types = ['daily'];
        else
            $types = ['weekly', 'monthly'];

        foreach ($types as $type)
            $this->proccess($type);

        echo "DONE\n";

        return 0;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
    protected function getArguments()
    {
        return array(
            array('type', InputArgument::REQUIRED, 'The type')
        );
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

    public function proccess($schedule_type)
    {
        switch ($schedule_type) {
            case 'daily':
                $field = 'daily_email_sent';
                $date = date('Y-m-d H:i:s', strtotime('+1 day'));
                $next_send_format = '+1 day';
                $date_from_format = '-1 day';
                break;
            case 'weekly':
                $field = 'weekly_email_sent';
                $date = date('Y-m-d H:i:s', strtotime('-6 day', strtotime(date('Y-m-d H:i:s'))));
                $next_send_format = '+7 day';
                $date_from_format = '-7 day';
                break;
            case 'monthly':
                $field = 'monthly_email_sent';
                $date = date('Y-m-d H:i:s', strtotime('last day of 0 month'));
                $next_send_format = 'first day of +1 month';
                $date_from_format = 'first day of -1 month';
                break;
            default:
                return null;
        }

        $reports = \Tobuli\Entities\Report::where($schedule_type, 1)->where($field, '<', $date)->get();

        if (empty($reports))
            return null;

        foreach ($reports as $report) {
            if ( ! $this->processManager->canContinue())
                break;

            if ( ! $this->processManager->lock($report->id))
                continue;

            $still = \Tobuli\Entities\Report::where($schedule_type, 1)->where($field, '<', $date)->find($report->id);

            if (empty($still)) {
                $this->processManager->unlock($report->id);
                continue;
            }

            if (array_key_exists($report->user_id, $this->users))
                $user = $this->users[$report->user_id];
            else
                $user = $this->users[$report->user_id] = UserRepo::find($report->user_id);

            if ( ! $user->isCapable()) {
                continue;
            }

            setActingUser($user);

            $last_send = Formatter::time()->convert($report->{$field});
            $send_time = date('Y-m-d', strtotime($last_send)) .' '. $report->{$schedule_type.'_time'};
            $next_send = date("Y-m-d H:i:s", strtotime(date('Y-m-d H:i:s', strtotime($send_time . $next_send_format))));
            $next_send = Formatter::time()->reverse($next_send);
            $current_time = date("Y-m-d H:i:s");

            /*
            echo "title: {$data['title']}" . PHP_EOL;
            echo "$current_time current_time" . PHP_EOL;
            echo "$last_send last_send" . PHP_EOL;
            echo "$send_time send_time" . PHP_EOL;
            echo "$next_send next_send user time" . PHP_EOL;
            */

            if (strtotime($next_send) > strtotime($current_time))
                continue;

            if (strtotime($report->{$field}) > strtotime($next_send))
                continue;

            $report->update([$field => date('Y-m-d H:i:s')]);

            $data = $report->toArray();

            $data['user'] = $user;
            $data['date_from'] = date(
                    'Y-m-d',
                    strtotime(
                        $date_from_format,
                        Formatter::time()->now()
                    )).' '.$data[$schedule_type.'_time'];
            $data['date_to'] = date(
                    'Y-m-d',
                    Formatter::time()->now()).' '.$data[$schedule_type.'_time'];

            if ( $schedule_type == 'daily' && !empty($data['from_format']) && !empty($data['to_format']) ) {
                $now_user_time  = strtotime( date('Y-m-d', Formatter::time()->now()) );
                $timestamp_from = strtotime( $data['from_format'], $now_user_time );
                $timestamp_to   = strtotime( $data['to_format'], $now_user_time );

                if ( $timestamp_from && $timestamp_to ) {
                    $data['date_from'] = date('Y-m-d H:i:s', $timestamp_from);
                    $data['date_to']   = date('Y-m-d H:i:s', $timestamp_to);
                }
            }

            if ($schedule_type == 'monthly') {
                $data['date_from'] = date('Y-m-d 00:00:00', strtotime($last_send . "first day of 0 month"));
                $data['date_to']   = date('Y-m-d 00:00:00', strtotime($last_send . "first day of +1 month"));
            }

            $generator = $this->reportManager->fromEntity($report, $data);
            $filename = $generator->isEnabled() ? $generator->save() : null;

            if (empty($filename))
                continue;

            $reportLog = ReportLogRepo::create([
                'user_id' => $report->user_id,
                'email' => $report->email,
                'title' => $report->title . ' ' . $data['date_from'].' - '.$data['date_to'],
                'type' => $report->type,
                'format' => $report->format,
                'size' => filesize($filename),
                'data' => file_get_contents($filename)
            ]);

            $emailTemplate = EmailTemplate::getTemplate('report', $user);

            try {
                $response = sendTemplateEmail($report->email, $emailTemplate, $data, [$filename]);
            } catch (\Exception $e) {
                Bugsnag::notifyException($e);
                $response = false;
            }

            if ($response) {
                if ( $response && !empty($response['status']) )
                    $reportLog->update( ['is_send' => true ] );
                else
                    $reportLog->update( ['error' => empty($response['error']) ? null : $response['error'] ] );
            }

            @unlink($filename);

            $this->processManager->unlock($data['id']);
        }
    }
}
