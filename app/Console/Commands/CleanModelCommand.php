<?php

namespace App\Console\Commands;

use App\Console\ProcessManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tobuli\Services\Cleaner\AbstractCleaner;
use Tobuli\Services\Cleaner\DeviceDateCleaner;
use Tobuli\Services\Cleaner\DeviceDaysCleaner;
use Tobuli\Services\Cleaner\ModelCleaner;
use Tobuli\Services\Cleaner\ModelSingleCleaner;

class CleanModelCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'model:clean {model} {type?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Models cleaner';

    protected $modelMap = [
        'event' => ModelCleaner::class,
        'device' => [
            'date' => DeviceDateCleaner::class,
            'days' => DeviceDaysCleaner::class,
        ],
        'report_log' => ModelSingleCleaner::class,
    ];

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        $model = $this->argument('model');
        $type = $this->argument('type');

        $cleaner = $this->resolveModelCleaner($model, $type);

        $this->processManager = new ProcessManager($this->name . ':' . $model . $type);

        if ( ! $this->processManager->canProcess()) {
            echo "Cant process \n";

            return;
        }

        $cleaner->clean();

        $this->line("Job done[OK]\n");
	}

    private function resolveModelCleaner(string $model, ?string $type): AbstractCleaner
    {
        if (!isset($this->modelMap[$model])) {
            throw new \InvalidArgumentException("`$model` model is not supported");
        }

        $cleanerClass = $this->modelMap[$model];

        if (is_array($cleanerClass)) {
            if (!isset($cleanerClass[$type])) {
                throw new \InvalidArgumentException("`$type` type on `$model` model is not supported");
            }

            $cleanerClass = $this->modelMap[$model][$type];
        }

        $params = $this->arguments() + $this->options();

        return (new $cleanerClass($params))
            ->setOutput(function ($success, $msg) {
                $success ? $this->line($msg) : $this->error($msg);
            });
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
        return [
            ['model', InputArgument::REQUIRED, 'Model'],
            ['type', InputArgument::OPTIONAL],
        ];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
            ['date', null, InputOption::VALUE_REQUIRED, 'Date until which created items are deleted.', null],
            ['limit', null, InputOption::VALUE_OPTIONAL, 'Max amount of items to be deleted.', 10000],
        ];
	}
}
