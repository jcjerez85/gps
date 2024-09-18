<?php

namespace Tobuli\Helpers;


use Illuminate\Support\Arr;
use Tobuli\Services\DatabaseService;

class Backup
{
    protected $settings;
    protected $ftp;
    protected $hive;

    public function __construct(array $settings)
    {
        $this->hive = new Hive();

        $this->settings = $settings;
    }

    /**
    * @return BackupFTP
    */
    public function setupFTP()
    {
        $settings = $this->settings;

        if ( ! empty($settings['type']) && $settings['type'] == 'auto')
        {
            $hiveSettings = $this->hive->getBackupServer();

            if (!$hiveSettings)
                throw new \Exception("Failed to get backup server data.");

            $settings = array_merge($this->settings, $hiveSettings);
        }

        $this->ftp = new BackupFTP(
            $settings['ftp_server'],
            $settings['ftp_username'],
            $settings['ftp_password'],
            $settings['ftp_port'],
            $settings['ftp_path']
        );
    }

    public function auto()
    {
        if (isset($this->settings['next_backup']) && time() < $this->settings['next_backup'])
            return false;

        $this->setNextBackup();

        $this->setupFTP();

        if ( ! $this->ftp()->getHost())
            return false;

        try {
            $this->db();
            $this->images();
        }
        catch(\Exception $e) {
            $this->setMessage('Error: ' . $e->getMessage(), 0);

            if (Arr::get($this->settings, 'type') == 'auto') {
                $hive = new Hive();
                $hive->backupServerError([
                    'code' => $e->getCode(),
                    'error' => $e->getMessage(),
                ]);
            }

            return false;
        }

        $this->setMessage(trans('front.successfully_uploaded'), 1);

        return true;
    }

    public function force()
    {
        if ( ! $this->ftp()->getHost())
            throw new \Exception('Not ftp server');

        if ( ! $this->ftp()->check())
            throw new \Exception(trans('front.login_failed'));


        $this->db();
        $this->images();
    }

    public function images()
    {
        $this->filesystem(images_path());
    }

    public function filesystem($path)
    {
        $command = "tar -cv " . $path;
        $filename = basename($path) . ".tar";

        $this->ftp()->process($command, $filename);
    }

    public function db()
    {
        DatabaseService::loadDatabaseConfig();

        $connections = Arr::where(config("database.connections"), function($config, $connection){
            return $config['driver'] == 'mysql';
        });

        foreach ($connections as $connection => $config) {
            $options = [
                "--single-transaction=TRUE",
                "--lock-tables=false",
                "-h {$config['host']}",
                "-u {$config['username']}",
                "--password={$config['password']}",
                "--databases {$config['database']}",
            ];

            $command = "mysqldump " . implode(" ", $options);

            $filename = "{$config['host']}-{$config['database']}-db.sql";

            $this->ftp()->process($command, $filename);
        }
    }

    public function check()
    {
        if ( ! $this->ftp()->check())
            throw new \Exception(trans('front.login_failed'));

        try {
            $this->ftp()->process('echo "test"', 'test.txt', false);
        } catch (\Exception $e) {
            throw new \Exception(trans('front.unexpected_error'));
        }
    }

    protected function setMessage($message, $status)
    {
        if ( ! isset($this->settings['messages']))
            $this->settings['messages'] = [];

        array_unshift($this->settings['messages'], [
            'status' => $status,
            'date' => date('Y-m-d H:i'),
            'path' => $this->settings['ftp_path'],
            'message' => $message
        ]);

        $this->settings['messages'] = array_slice($this->settings['messages'], 0, 5);

        $this->writeSettings();
    }

    protected function writeSettings($retry = 0)
    {
        try {
            settings('backups', $this->settings);
        } catch (\Exception $e) {
            if ($retry > 3) {
                throw $e;
            }

            sleep(30);
            $this->writeSettings(++$retry);
        }
    }

    protected function setNextBackup()
    {
        $this->settings['next_backup'] = strtotime(date('Y-m-d', strtotime('+'.$this->settings['period'].' days')).' '.$this->settings['hour']);

        settings('backups.next_backup', $this->settings['next_backup']);
    }

    /**
    * @return BackupFTP
    */
    protected function ftp()
    {
        if (is_null($this->ftp))
            $this->setupFTP();

        return $this->ftp;
    }
}