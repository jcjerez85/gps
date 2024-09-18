<?php namespace App\Console\Commands;

use App\Console\PositionsStack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Tobuli\Entities\Config;
use Exception;
use App\Console\ProcessManager;
use File;
use Tobuli\Entities\Device;
use Tobuli\Helpers\Hive;
use Tobuli\Helpers\Tracker;
use Tobuli\Services\DeviceConfigUpdateService;

class CheckServerCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'server:check';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
	public function handle(Config $config)
	{
	    $tracker = new Tracker();

		$curl = new \Curl;
		$curl->follow_redirects = false;
		$curl->options['CURLOPT_SSL_VERIFYPEER'] = false;
		$curl->options['CURLOPT_TIMEOUT'] = 30;

        $hive = new Hive();

		$traccar_restart = '';
		try {
			$autodetect = ini_get('auto_detect_line_endings');
			ini_set('auto_detect_line_endings', '1');
			$lines = file('/var/spool/cron/root', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			ini_set('auto_detect_line_endings', $autodetect);
			foreach ($lines as $key => $line) {
				if (strpos($line, 'tracker:restart') !== false) {
					list($time) = explode('php', $line);
					$traccar_restart = trim($time);
					break;
				}
				//$text .= $line."\r\n";
			}
		}
		catch(\Exception $e) {

		}

		$host= gethostname();
		$ip = gethostbyname($host);

		if (!is_numeric(substr($ip, 0, 1))) {
			$command = "/sbin/ifconfig eth0 | grep \"inet addr\" | awk -F: '{print $2}' | awk '{print $1}'";
			$ip = exec($command);
		}

		$cfg = settings('jar_version');
		if (empty($cfg)) {
            settings('jar_version', 1);
		}
		$jar_version = empty($cfg) ? 1 : $cfg;

		$cpu = exec("ps --no-heading -o pcpu -C httpd | awk '{s+=$1} END {print s}'");
		$cores = exec("nproc");
		$cpu = (empty($cores) || empty($cpu)) ? 0 : round(($cpu / $cores), 2);
		$ram_used = round(exec("free | awk 'FNR == 2 {print $3/1000000}'"), 2);
		$ram_all = round(exec("free | awk 'FNR == 2 {print ($3+$4)/1000000}'"), 2);
		$disk_total = disk_total_space("/");
		$disk_free = disk_free_space("/");
		$disk_used = $disk_total - $disk_free;
		$traccar_status = boolval(strpos(exec("sudo service traccar status"), 'traccar is running') !== false) ? 1 : 0;

        $devices_online = Device::online(6)->count();
        $devices_total = Device::count();

        try {
            $redis = Redis::connection();
        }
        catch (\Exception $e) {
            $redis = FALSE;
        }

        $position_count = 0;
        if ($redis) {
            $position_count += (new PositionsStack())->count();
        }

        // Check if memcached php module loaded
        $memcached = class_exists('Memcached');

        // Check if memcached php server is up
        $memcachedServerRunning = false;
        if ($memcached) {
            try {
                $memcachedStats = Cache::store('memcached')->getMemcached()->getStats();
                $memcachedServerRunning = true;
            } catch ( Exception $e) {}
        }

        // try {
        //     $response = json_decode($curl->get('http://hive.gpswox.com/check_jar', [
        //         'google_maps_key' => settings('main_settings.google_maps_key'),
        //         'version' =>$jar_version,
        //         'app_version' => config('tobuli.version'),
        //         'cpu' => $cpu,
        //         'ram' => compact('ram_used', 'ram_all'),
        //         'disk' => compact('disk_total', 'disk_used'),
        //         'traccar_restart' => $traccar_restart,
        //         'traccar_status' => $traccar_status,
        //         'redis_status' => $redis ? 1 : 0,
        //         'redis_keys' => $position_count,
        //         'memcached_module_status' => $memcached ? 1 : 0,
        //         'memcached_server_status' => $memcachedServerRunning ? 1 : 0,
        //         'cores' => $cores,
        //         'devices_online' => $devices_online,
        //         'devices_total' => $devices_total,
        //         'admin_user' => config('app.admin_user'),
        //         'name' => config('app.server'),
        //         'type' => config('tobuli.type'),
        //         'ip' => $ip,
        //         'php' => phpversion(),
        //     ]), TRUE);
        // } catch (Exception $e) {
        // }
		$response = false;

        $this->processManager = new ProcessManager($this->name, $timeout = 3600, $limit = 1);

        if ( ! $this->processManager->canProcess())
        {
            echo "Cant process \n";
            return -1;
        }

        if ($response && array_key_exists('autodeploy', $response))
        {
            $autodeployMark = storage_path('autodeploy');

            if ($response['autodeploy'] && File::exists($autodeployMark))
                File::delete($autodeployMark);

            if ( ! $response['autodeploy'] && ! File::exists($autodeployMark))
                File::put($autodeployMark, '');
        }

		if (empty(settings('last_ports_modification'))) {
            settings('last_ports_modification', 0);
		}

        if (empty(settings('last_config_modification'))) {
            settings('last_config_modification', 0);
        }

        $last_ports_modification = settings('last_ports_modification');
        $last_config_modification = settings('last_config_modification');


        $configUpdateService = new DeviceConfigUpdateService();
        $last_apns_modification = settings('last_apns_modification');
        $last_device_configs_modification = settings('last_device_configs_modification');

        if ((isset($response['apns']) && $response['apns']['last'] > $last_apns_modification)) {
            $configUpdateService->updateApnConfigs( $hive->getApns() );
            settings('last_apns_modification', $response['apns']['last']);
        }

        if ((isset($response['device_configs']) && $response['device_configs']['last'] > $last_device_configs_modification)) {
            $configUpdateService->updateDeviceConfigs( $hive->getDeviceConfigs() );
            settings('last_device_configs_modification', $response['device_configs']['last']);
        }

		if (isset($response['ports']) && $response['ports']['last'] > $last_ports_modification) {
			parsePorts($response['ports']['items']);

            settings('last_ports_modification', $response['ports']['last']);
            settings('last_config_modification', $response['configs']['last']);
		}
		else {
			if (isset($response['configs']) && $response['configs']['last'] > $last_config_modification) {
                settings('last_config_modification', $response['configs']['last']);
			}
		}

		if ((isset($response['ports']) && $response['ports']['last'] > $last_ports_modification) || (isset($response['configs']) && $response['configs']['last'] > $last_config_modification)) {
			$tracker->config()->update();
            $tracker->restart();
		}

		if (!empty($response['status']) && !empty($response['url'])) {
			try {
                if ($tracker->upgrade($response['url']))
                    settings('jar_version', $response['version']);
            } catch (Exception $exception) {
			    $this->error($exception->getMessage());
            }
		}

		$date = date('Y-m-d H:i:s', strtotime('-1 days'));
		DB::statement("DELETE FROM sms_events_queue WHERE created_at < '{$date}'");

        $this->line('Ok');

        return 0;
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
