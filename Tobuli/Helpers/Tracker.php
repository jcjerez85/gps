<?php

namespace Tobuli\Helpers;

use App\Events\Tracker\RestartFail;
use App\Events\Tracker\RestartSuccess;
use App\Jobs\TrackerRestart;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tobuli\Entities\User;

class Tracker {

    const FILE_CURRENT = '/opt/traccar/tracker-server.jar';
    const FILE_BACKUP = '/opt/traccar/tracker-server-back.jar';
    const FILE_NEW = '/opt/traccar/tracker-server-current.jar';
    const FILE_PID = '/opt/traccar/bin/traccar.pid';
    const LOCK_KEY = 'tracker.restart.process';
    const LOCK_TIME = 30;

    const RESTART_ATTEMPT = 2;
    const RESTART_TIMEOUT = 60;
    const DOWNLOAD_TIMEOUT = 900;

    /**
     * @var string[]
     */
    private static $successRestartMessages = [
        'running: PID',
        'Service traccar started',
    ];

    /**
     * @var string[]
     */
    private static $successStatusMessages = [
        'traccar is running',
    ];

    /**
     * @var User|null $actor User initiated action
     */
    protected $actor;

    /**
     * @var TrackerConfig $config
     */
    protected $config;

    /**
     * @param User $user
     * @return $this
     */
    public function actor(User $user)
    {
        $this->actor = $user;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function restart()
    {
        if ( ! app()->runningInConsole()) {
            $this->restartJob();
            return null;
        }

        if ($this->locked() || !$this->lock())
            return null;

        $status = $this->restartProcess();

        $this->unlock();

        if ($status)
            event(new RestartSuccess($this->actor));
        else
            event(new RestartFail($this->actor));

        return $status;
    }

    /**
     * @return bool
     */
    public function status()
    {
        $process = Process::fromShellCommandline('/etc/init.d/traccar status');
        $process->run();

        while ($process->isRunning()) {}

        $output = $process->getOutput();

        return $this->hasOutput($output, self::$successStatusMessages);
    }

    /**
     * @param string $url
     * @return boolean
     * @throws \Exception
     */
    public function upgrade($url)
    {
        $this->backup();
        $this->download($url);

        File::copy(self::FILE_NEW, self::FILE_CURRENT);

        if ( ! $this->restart()) {
            $this->reverse();

            return false;
        }

        File::delete(self::FILE_NEW);
        File::delete(self::FILE_BACKUP);

        return true;
    }

    /**
     * @return TrackerConfig
     */
    public function config()
    {
        if (is_null($this->config))
            $this->config = new TrackerConfig();

        return $this->config;
    }

    public function sendCommand($data) {
        return $this->api('api/commands/send', $data);
    }

    /**
     * @param int $attempt
     * @return bool
     */
    protected function restartProcess($attempt = 1) {
        $this->serviceKill();
        $this->wait();

        if ($this->deadProcess())
            $this->servicePidDelete();

        if ($this->serviceRestart())
            return true;

        if ($attempt > self::RESTART_ATTEMPT)
            return false;

        return $this->restartProcess(++$attempt);
    }

    protected function restartJob()
    {
        dispatch(new TrackerRestart($this->actor));
    }

    /**
     * @return bool
     */
    public function restartRemote() {
        // $curl = new \Curl;
        // $curl->follow_redirects = false;
        // $curl->options['CURLOPT_SSL_VERIFYPEER'] = false;

        // $response = $curl->post('http://hive.gpswox.com/servers/restart_traccar', [
        //     'admin_user' => config('app.admin_user'),
        //     'name' => config('app.server'),
        //     'reason' => 'tracker'
        // ]);

        return $response == 'OK' ? true : false;
    }

    /**
     * @return bool
     */
    protected function serviceRestart()
    {
        $output = shell_exec('/etc/init.d/traccar restart');

        if ( ! $this->hasOutput($output, self::$successRestartMessages))
            return false;

        return true;


        $process = Process::fromShellCommandline('/etc/init.d/traccar restart');
        $process->run();
        $process->setTimeout(self::RESTART_TIMEOUT);

        while ($process->isRunning()) {
            if ($process->checkTimeout()) break;
        }

        if ( ! $process->isSuccessful())
            return false;

        $output = $process->getOutput();

        if ( ! $this->hasOutput($output, self::$successRestartMessages))
            return false;

        return true;
    }

    /**
     * @return bool|null
     */
    protected function servicePidDelete()
    {
        if ( ! file_exists(self::FILE_PID)) {
            return null;
        }

        $process = Process::fromShellCommandline("sudo rm -f " . self::FILE_PID);
        $process->run();

        while ($process->isRunning()) {}

        return $process->isSuccessful();
    }

    /**
     * @return void
     */
    protected function serviceKill()
    {
        $process = Process::fromShellCommandline('killall java');
        $process->run();
        while ($process->isRunning()) {}

        $process = Process::fromShellCommandline('kill $(ps aux | grep "[j]ava" | awk "{print $2}")');
        $process->run();
        while ($process->isRunning()) {}
    }

    /**
     * @param string $output
     * @param array $messages
     * @return bool
     */
    protected function hasOutput($output, array $messages)
    {
        $properOutputs = array_filter($messages, function($message) use ($output) {
            return strpos($output, $message) !== false;
        });

        return empty($properOutputs) ? false : true;
    }

    /**
     * @throws \Exception
     */
    protected function backup() {
        if (File::exists(self::FILE_BACKUP))
            File::delete(self::FILE_BACKUP);

        File::copy(self::FILE_CURRENT, self::FILE_BACKUP);

        if ( ! File::exists(self::FILE_BACKUP))
            throw new \Exception('Failed to create tracker backup file');
    }

    protected function reverse() {
        File::copy(self::FILE_BACKUP, self::FILE_CURRENT);

        $this->restart();
    }

    /**
     * @param $url
     * @throws \Exception
     */
    protected function download($url) {
        $process = Process::fromShellCommandline("wget -O " . self::FILE_NEW . " $url");
        $process->setTimeout(self::DOWNLOAD_TIMEOUT);
        $process->run();

        while ($process->isRunning()) {}

        if ( ! $process->isSuccessful())
            throw new ProcessFailedException($process);

        $size = filesize_remote($url);

        if (false === $size)
            throw new \Exception('Unable fetch file size');

        if (File::size(self::FILE_NEW) != $size)
            throw new \Exception('Failed to download tracker');
    }


    public function getUrl($external = false)
    {
        $config = config('tracker');

        $url = $config['web.url'];

        $url = str_replace('https://', 'http://', $url);
        $url = str_replace(config('app.server'), 'localhost', $url);

        if ($external) {
            $url = Str::contains($url, 'localhost') ? url('/') : $url;
        } else {
            $url = str_replace(config('app.server'), 'localhost', $url);
        }

        $url = str_replace('https://', 'http://', $url);
        $url = trim($url, '/');

        return $url;
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return array
     */
    protected function api($endpoint, $data = [])
    {
        $curl = new \Curl;

        $curl->headers = [
            'Authorization' => 'Basic ' . base64_encode("admin:" . config('app.admin_user')),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $curl->options['CURLOPT_HEADER'] = false;
        $curl->options['CURLOPT_RETURNTRANSFER'] = true;

        $config = config('tracker');
        $url = $this->getUrl() . ':' . $config['web.port'];

        try {
            $response = $curl->post($url . '/' . $endpoint, json_encode($data));
            $message = $this->parseResponseError($response);
        } catch (\CurlException $exception) {
            $response = null;
            $message = trans('admin.unable_to_connect_to_tracker_server');
        }

        $message = isset($message) ? $message : null;
        $status = isset($message) ? 0 : 1;

        return [
            'status'   => $status,
            'message'  => $message,
            'response' => $response,
        ];
    }

    /**
     * @param $response
     * @return string|null
     */
    protected function parseResponseError($response) {
        $decoded_response = json_decode($response, true);

        if (is_null($decoded_response))
            return "Failed ($response)";

        if (array_key_exists('message', $decoded_response))
            return is_null($decoded_response['message']) ? $decoded_response['details'] : $decoded_response['message'];

        return null;
    }

    /**
     * @return bool
     */
    protected function deadProcess()
    {
        if ( ! File::exists(self::FILE_PID))
            return false;

        if ($this->status())
            return false;

        return true;
    }

    protected function lastAttempt($attempt)
    {
        return $attempt == self::RESTART_ATTEMPT;
    }

    protected function lock()
    {
        return Redis::connection()->set(self::LOCK_KEY, time(), 'ex', self::LOCK_TIME, 'nx');
    }

    protected function unlock()
    {
        Redis::connection()->del(self::LOCK_KEY);
    }

    protected function locked()
    {
        return ! empty(Redis::connection()->get(self::LOCK_KEY));
    }

    protected function wait()
    {
        sleep(5);
    }
}
