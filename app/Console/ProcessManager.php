<?php namespace App\Console;

use App;
use Illuminate\Support\Facades\Redis;
use Predis\Pipeline\Pipeline;

class ProcessManager {

    protected $redis;

    protected $process_limit = 5;

    protected $timeout = 60;

    protected $timeover;

    protected $group;

    public $unlocking = true;

    public $key;

    public function __construct($group, $timeout = 60, $limit = 1)
    {
        $this->redis = Redis::connection('process');

        $this->group = $group;

        $this->timeout = $timeout;

        $this->timeover = time() + $this->timeout;

        $this->process_limit = $limit;

        if ($this->process_limit)
            $this->cleanKilledProcess();

        $this->register();
    }

    function __destruct()
    {
        $this->unregister();
    }

    public function canContinue() {
        if ($this->timeover < time())
            return false;

        if (App::isDownForMaintenance())
            return false;

        return true;
    }

    public function canProcess() {
        return $this->canContinue() && ! $this->reachedLimit();
    }

    public function lock($id)
    {
        $key = 'processing.' . $this->group . '.' . $id;

        return $this->redis->set($key, $this->key, 'ex', $this->timeout, 'nx') ? true : false;
    }

    public function unlock($id)
    {
        $key = 'processing.' . $this->group . '.' . $id;

        $this->redis->del($key);
    }

    public function prolongLock($id): bool
    {
        $key = 'processing.' . $this->group . '.' . $id;

        $pipeData = $this->redis->pipeline(function(Pipeline $pipe) use ($key) {
            $pipe->del($key);
            $pipe->set($key, $this->key, 'ex', $this->timeout, 'nx');
        });

        return (bool)last($pipeData);
    }

    private function unlockKeys($process_key = null)
    {
        if ( ! $process_key)
            $process_key = $this->key;

        $keys = $this->redis->keys('processing.' . $this->group . '.*');

        foreach($keys as $key) {
            $process = $this->redis->get($key);

            if ($process != $process_key)
                continue;

            $this->redis->del($key);
        }
    }

    private function reachedLimit()
    {
        if ( ! $this->process_limit)
            return false;

        $processes = $this->redis->keys('process.' . $this->group . '.*');

        return $processes && (count($processes) > $this->process_limit);
    }

    private function cleanKilledProcess()
    {
        $keys = $this->redis->keys('process.' . $this->group . '.*');

        foreach ($keys as $key) {
            $process = $this->redis->get($key);

            $process = json_decode($process);

            if ( ! $process) {
                $this->redis->del($key);
                continue;
            }

            // 6h
            if (time() - $process->timeover > ($this->timeout * 5)) {
                $this->unregister($process->key);
                continue;
            }

            // process is running?
            if (file_exists('/proc/'.$process->pid))
                continue;

            $this->unregister($process->key);
        }
    }

    private function register()
    {
        $this->key = md5( $this->group . time() . str_shuffle('QWERTYUIOOPASDFGHJKLZXCVBNMQWERTYUIOPASD') );

        $key = 'process.' . $this->group . '.' . $this->key;

        $this->redis->set($key, json_encode([
            'pid'      => getmypid(),
            'key'      => $this->key,
            'timeover' => $this->timeover
        ]));
    }

    public function unregister($process_key = null)
    {
        if ( ! $process_key)
            $process_key = $this->key;

        $this->redis->del('process.' . $this->group . '.' . $process_key);

        if ($this->unlocking)
            $this->unlockKeys($process_key);
    }

    public function disableUnlocking()
    {
        $this->unlocking = false;
    }
}