<?php namespace Tobuli\Services;

use Tobuli\Entities\DeviceCamera;
use Tobuli\Entities\File\DeviceCameraMedia;
use Symfony\Component\Process\Process;

class FtpUserService
{
    const CONFIG_FILE = "/etc/vsftpd/vsftpd.conf";
    const USER_LIST_FILE = "/etc/vsftpd/user_list";
    const SSHD_CONFIG_FILE = "/etc/ssh/sshd_config";
    const RESTART_VSFTPD_COMMAND = "service vsftpd restart";
    const RESTART_SSHD_COMMAND = "systemctl restart sshd";
    const START_VSFTPD_ON_BOOT_COMMAND = "chkconfig --levels 235 vsftpd on";

    private $deviceMedia;

    public function __construct()
    {
        $this->deviceMedia = new DeviceCameraMedia();
    }

    public function generateCameraFtpUser(DeviceCamera $camera)
    {
        $camera = $this->generateUniqId($camera);

        $path = $this->deviceMedia->getDirectory($camera);

        $password = hash('fnv164', uniqid($camera->username, true));

        $camera->ftp_password = $password;
        $camera->save();

        $result = $this->createFtpUser($camera->ftp_username, $password, $path);

        return $result;
    }

    public function deleteCameraFtpUser(DeviceCamera $camera)
    {
        $this->removeFtpUser($camera->ftp_username);
    }

    public function setup()
    {
        $this->installPackages();
        $this->configureVsftpd();

        $this->executeCommand(self::START_VSFTPD_ON_BOOT_COMMAND);
    }

    public function removeFtpUser($username, $deleteHome = false)
    {
        $this->executeCommand('userdel '.($deleteHome ? '-r ' : '').$username);
    }

    public function createFtpUser($username, $password, $homePath)
    {
        $result = ['success' => 1];

        $commands = [
            "useradd {$username}",
            "echo '{$username}:$password' | chpasswd",
            "mkdir -p {$homePath}",
            "usermod -m -d {$homePath} {$username}",
            "chown {$username}:{$username} {$homePath}",
            "chmod 0707 {$homePath}",
        ];

        foreach ($commands as $command) {
            $process = $this->executeCommand($command);

            $error = $process->getErrorOutput();

            if (!$process->isSuccessful() && !empty($error) && !preg_match('/(?=.*usermod)(?=.*exists)/', $error)) {
                $result = ['error' => "{$command}: ".$error];
                break;
            }
        }

        if (isset($result['error'])) {
            $this->removeFtpUser($username, true);
        } else {
            $this->disableSshAccess($username);

            $this->restartSshService();
        }

        return $result;
    }

    private function generateUniqId($camera)
    {
        do {
            $camera->ftp_username = uniqid($camera->device_id.$camera->id);
        } while (DeviceCamera::where('ftp_username', $camera->ftp_username)->first()
            || file_exists($this->deviceMedia->getDirectory($camera)));

        $camera->save();

        return $camera;
    }

    /**
     * Disable ssh access for user in /etc/ssh/sshd_config file
     *
     * @param String $user
     * @return boolean
     */
    private function disableSshAccess($user)
    {
        if (!is_file(self::SSHD_CONFIG_FILE)) {
            return false;
        }

        $config = file(self::SSHD_CONFIG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($config === false) {
            return false;
        }

        $index = -1;

        foreach ($config as $i => $line) {
            if (strpos(trim($line), 'DenyUsers') === 0) {
                $index = $i;

                break;
            }
        }

        if ($index == -1) {
            $config[] = "DenyUsers {$user}";
        } else {
            $config[$index] = rtrim($config[$index]).' '.$user;
        }

        $command = "echo \"".implode(PHP_EOL, $config)."\" | tee ".self::SSHD_CONFIG_FILE;

        $process = $this->executeCommand($command);

        if (!$process->isSuccessful() || $process->getErrorOutput()) {
            return false;
        }

        return true;
    }

    private function restartSshService()
    {
        $this->executeCommand(self::RESTART_VSFTPD_COMMAND);

        $this->executeCommand(self::RESTART_SSHD_COMMAND);
    }

    private function packageExists($name)
    {
        $result = true;
        $command = "yum list installed {$name}";

        $process = $this->executeCommand($command);

        $output = $process->getOutput();

        if (strpos(strtolower($output), 'error: no matching packages to list') !== false) {
            $result = false;
        }

        return $result;
    }

    private function installPackages()
    {
        $commands = [
            'vsftpd' => 'sudo yum install vsftpd -y',
            'ftp' => 'sudo yum install ftp -y',
            'quota' => 'sudo yum install quota -y',
        ];

        foreach ($commands as $name => $command) {
            if ($this->packageExists($name)) {
                continue;
            }

            $this->executeCommand($command);
        }
    }

    /*
    * Set vsftpd config in '/etc/vsftpd/vsftpd.conf'
    *
    */
    private function configureVsftpd()
    {
        $settings = [
            'listen=NO',
            'listen_ipv6=YES',
            'anonymous_enable=NO',
            'local_enable=YES',
            'write_enable=YES',
            'local_umask=022',
            'dirmessage_enable=YES',
            //'use_localtime=YES',
            'xferlog_enable=YES',
            'xferlog_std_format=YES',
            'connect_from_port_20=YES',
            'chroot_local_user=YES',
            'pam_service_name=vsftpd',
            'userlist_enable=YES',
            //'userlist_file=/etc/vsftpd.userlist',
            //'userlist_deny=NO', //default yes
            'allow_writeable_chroot=YES',
            'tcp_wrappers=YES',
            'setproctitle_enable=YES',
        ];

        if (!is_file(self::CONFIG_FILE)) {
            return false;
        }

        $command = "echo \"".implode(PHP_EOL, $settings)."\" | tee ".self::CONFIG_FILE;

        $process = $this->executeCommand($command);

        if (!$process->isSuccessful() || $process->getErrorOutput()) {
            return false;
        }

        return true;
    }

    /**
     * Executes command on server
     * 
     * @param string $command Command to execute
     * @return \Symfony\Component\Process\Process
     */
    private function executeCommand($command)
    {
        $process = Process::fromShellCommandline($command);
        $process->run();
        $process->wait();

        return $process;
    }
}
