<?php

namespace Tobuli\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Process\Process;

class Server {

    const SPACE_PERCENTAGE_WARNING = 98;

    public function ip()
    {
        $ip = config('server.floating_ip');

        if ($ip)
            return $ip;

        try {
            $prefix = php_sapi_name() . '.server.';

            $ip = Cache::get($prefix.'ip');

            if ($ip)
                return $ip;

            //$ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null;

            if (!$ip || $this->isPrivateIP($ip))
                $ip = @exec('curl -s ipinfo.io/ip');

            $ip = trim($ip);

            if (ip2long($ip) && !$this->isPrivateIP($ip))
                Cache::put($prefix.'ip', $ip, 15 * 60);

        } catch (\Exception $e){};

        return $ip;
    }

    public function isPrivateIP($value) {
        if ($value == '127.0.0.1')
            return true;

        if (strpos($value, '192.168.') === 0)
            return true;

        if (strpos($value, '10.') === 0)
            return true;

        return false;
    }

    public function hostname()
    {
        $hostname = null;

        try {
            $prefix = php_sapi_name() . '.server.';

            $hostname = Cache::get($prefix.'hostname');

            if ($hostname)
                return $hostname;

            $hostname = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;

            if (empty($hostname))
                $hostname = gethostname();

            if ($hostname && !$this->isPrivateIP($hostname))
                Cache::put($prefix.'hostname', $hostname, 5 * 60);

        } catch (\Exception $e){};

        return $hostname;
    }

    public function url()
    {
        $url = config('app.url');

        if ( !empty($url) && $url != 'http://localhost' )
            return $url;

        $hostname = $this->hostname();

        if (!$hostname)
            $hostname = $this->ip();

        $protocol = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';

        return $protocol . $hostname;
    }

    public function lastUpdate()
    {
        return date('Y-m-d H:i:s', File::lastModified(base_path('server.php')));
    }

    public function isAutoDeploy()
    {
        return ! File::exists(storage_path('autodeploy'));
    }

    public function isDisabled()
    {
        return file_exists('/var/www/html/disabled.txt');
    }

    public function isApiDisabled()
    {
        return file_exists('/var/www/html/apidisabled');
    }

    public function isDatabaseLocal()
    {
        $host = config('database.connections.mysql.host');

        if (in_array($host, ['localhost', '127.0.0.1']))
            return true;

        return false;
    }

    public function isSpacePercentageWarning()
    {
        return ($this->wwwSpacePercentage() > self::SPACE_PERCENTAGE_WARNING ||
            $this->traccarSpacePercentage() > self::SPACE_PERCENTAGE_WARNING ||
            $this->databaseSpacePercentage() > self::SPACE_PERCENTAGE_WARNING) ? true : false;
    }

    public function hasDeviceLimit()
    {
        return config('server.device_limit', 0) > 1;
    }

    public function getDeviceLimit()
    {
        if ($this->hasDeviceLimit())
            return config('server.device_limit');

        return null;
    }

    public function setMemoryLimit($limit, $force = false) {
        if (!$force && $limit < $this->getMemoryLimit())
            return;

        ini_set('memory_limit', $limit);
    }

    public function getMemoryLimit() {
        return ini_get('memory_limit');
    }

    public function databaseSpacePercentage()
    {
        try {
            $directory = exec('mysql -u root -p'. config('database.connections.mysql.password') .' -Bse "select @@datadir;"');

            return $this->spaceUsePercentage($directory);

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function wwwSpacePercentage()
    {
        try {
            $directory = storage_path();

            return $this->spaceUsePercentage($directory);

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function traccarSpacePercentage()
    {
        try {
            $directory = config('tobuli.logs_path');

            return $this->spaceUsePercentage($directory);

        } catch (\Exception $e) {
            return 0;
        }
    }

    private function spaceUsePercentage($directory)
    {
        return 100 - round(disk_free_space($directory) / disk_total_space($directory) * 100, 1);
    }

    public function databaseFreeSpace()
    {
        try {
            $directory = exec('mysql -u root -p'. config('database.connections.mysql.password') .' -Bse "select @@datadir;"');

            return disk_free_space($directory);

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function wwwFreeSpace()
    {
        try {
            $directory = storage_path();

            return disk_free_space($directory);

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function traccarFreeSpace()
    {
        try {
            $directory = config('tobuli.logs_path');

            return disk_free_space($directory);

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function databaseTotalSpace()
    {
        try {
            $directory = exec('mysql -u root -p'. config('database.connections.mysql.password') .' -Bse "select @@datadir;"');

            return disk_total_space($directory);

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function wwwTotalSpace()
    {
        try {
            $directory = storage_path();

            return disk_total_space($directory);

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function traccarTotalSpace()
    {
        try {
            $directory = config('tobuli.logs_path');

            return disk_total_space($directory);

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function isOnSameDisk()
    {
        $db_disk = exec('stat -c "%d" ' .
            exec('mysql -u root -p'. config('database.connections.mysql.password') .' -Bse "select @@datadir;"'));

        $traccar_disk = exec('stat -c "%d" ' . config('tobuli.logs_path'));

        $storage_disk = exec('stat -c "%d" ' . storage_path());

        return ($traccar_disk == $db_disk && $storage_disk == $db_disk);
    }

    public function statusServices()
    {
        $services = [
            'db'         => false,
            'http'       => false,
            'redis'      => false,
            'traccar'    => false,
            'supervisor' => false
        ];

        $services['http'] = $this->process('sudo service httpd status', 'is running');
        $services['traccar'] = $this->process('sudo service traccar status', 'is running');
        $services['supervisor'] = $this->process('sudo service supervisord status', 'is running');

        try {
            \DB::raw('SELECT 1+1');

            $services['db'] = true;
        }
        catch (\Exception $e) {}

        try {
            $redis = Redis::connection();

            $services['redis'] = true;
        }
        catch (\Exception $e) {}

        return $services;
    }

    protected function process($command, $expect = null)
    {
        $process = Process::fromShellCommandline($command);
        $process->run();

        while ($process->isRunning()) {
            // waiting for process to finish
        }

        echo $process->getOutput() . '<br>';

        if ( ! $process->isSuccessful())
            return false;

        if (is_null($expect))
            return true;

        echo $expect . '<br>';

        return strpos($process->getOutput(), $expect) !== false;
    }
}