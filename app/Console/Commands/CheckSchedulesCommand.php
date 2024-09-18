<?php namespace App\Console\Commands;

use App\Jobs\RunCommandScheduleJob;
use Illuminate\Console\Command;
use Tobuli\Entities\Schedule;
use Tobuli\Services\Schedule\Scheduler;

class CheckSchedulesCommand extends Command
{
    private static $scheduleJobMap = [
        'CommandSchedule' => RunCommandScheduleJob::class,
    ];

    private $scheduler;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'schedules:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run scheduled tasks.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->scheduler = new Scheduler();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Schedule::mustRun()->with('subject')
            ->orderBy('id')
            ->chunk(300, function ($schedules) {
            foreach ($schedules as $schedule)
            {
                $schedule_subject = class_basename($schedule->subject);

                if ( ! array_key_exists($schedule_subject, self::$scheduleJobMap))
                    continue;

                dispatch(
                    $this->job($schedule, $schedule_subject)
                );

                $this->scheduler->reschedule($schedule);
            }
        });

        echo 'Done';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    private function job($schedule, $schedule_subject)
    {
        return new self::$scheduleJobMap[$schedule_subject]($schedule->subject);
    }
}
