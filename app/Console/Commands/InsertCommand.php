<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;
use CustomFacades\Repositories\DeviceRepo;
use App\Console\PositionsStack;
use App\Console\ProcessManager;
use App\Console\PositionsWriter;
use Tobuli\Entities\UnregisteredDevice;

class InsertCommand extends Command
{
    protected $debug = false;

    /**
     * @var ProcessManager
     */
    protected $processManager;

    /**
     * @var PositionsStack
     */
    protected $positionsStack;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'insert:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->debug = ! empty($this->argument('debug'));

        DB::disableQueryLog();

        $timeout = config('tobuli.process.insert_timeout');
        $limit = config('tobuli.process.insert_limit');

        $this->positionsStack = new PositionsStack();
        $this->processManager = new ProcessManager($this->name, $timeout, $limit);

        if ( ! $this->processManager->canProcess()) {
            echo "Cant process.";
            return;
        }

        while ($this->processManager->canContinue())
        {
            $start = microtime(true);

            $imei = $this->process();

            if($this->debug)
                $this->line('Process: '.(microtime(true) - $start));

            if ($imei)
            {
                $this->processManager->unlock($imei);
                continue;
            }

            sleep(1);
        }
    }

    private function process()
    {
        return $this->processByList();
    }

    private function processByList()
    {
        $start = microtime(true);

        $imei = $this->getListUnlockedImei();

        if($this->debug) {
            $this->line('');
            $this->line('Getting imei: ' . (microtime(true) - $start));
        }

        if ( ! $imei)
            return false;

        $start = microtime(true);

        $device = DeviceRepo::whereImei($imei);

        if ( ! $device)
        {
            $data = $this->positionsStack->getData($imei, false);

            if ( ! $data) {
                $this->positionsStack->deleteImei($imei);
                return $imei;
            }

            $device = DeviceRepo::getByImeiProtocol($data['imei'], $data['protocol']);
        }

        if($this->debug)
            $this->line('Getting device: '.(microtime(true) - $start));


        if ( ! $device) {
            UnregisteredDevice::increase(
                Arr::get($data, 'imei'),
                Arr::get($data, 'protocol'),
                Arr::get($data, 'attributes.ip'),
                $this->positionsStack->count($imei)
            );

            $this->positionsStack->deleteImei($imei);

        } elseif ($device->active) {
            $start = microtime(true);
            $writer = new PositionsWriter($device, $this->debug);
            $writer->runList($imei);
            if($this->debug)
                $this->line('All writer: '.(microtime(true) - $start));

        } else {
            $this->positionsStack->deleteImei($imei);
        }

        return $imei;
    }

    private function getListUnlockedImei()
    {
        $imei = null;

        do
        {
            $_imei = $this->positionsStack->next();

            if ( ! $this->isValidImei($_imei)) {
                $this->positionsStack->deleteImei($_imei);
                continue;
            }

            if ( ! $this->processManager->lock($_imei))
                continue;

            $imei = $_imei;

        } while ($_imei && is_null($imei));

        return $imei;
    }

    private function isValidImei($imei)
    {
        if (empty($imei))
            return false;

        return !preg_match('/[^A-Za-z0-9.#\\-$]/', $imei);
    }

    protected function getArguments()
    {
        return array(
            array('debug', InputArgument::OPTIONAL, 'Debug')
        );
    }
}
