<?php

namespace Tobuli\Helpers;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupFTP
{
    protected $host;
    protected $user;
    protected $pass;
    protected $port;
    protected $path;

    public function __construct($host, $user, $pass, $port, $path)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
        $this->path = rtrim($path, '/') . '/';
    }

    public function getHost()
    {
        return $this->host;
    }

    public function check()
    {
        $connection = ftp_connect($this->host, $this->port);

        if ( ! @ftp_login($connection, $this->user, $this->pass)) {
            return false;
        }

        return true;
    }

    public function process($command, $filename, $gzip = true)
    {
        $this->run( $this->buildCommand($command, $filename, $gzip) );
    }

    protected function buildCommand($command, $filename, $gzip = true)
    {
        $filename = $this->buildFilename($filename, $gzip);

        $commands = [];
        $commands[] = $command;

        if ($gzip)
            $commands[] = "gzip -9";

        $commands[] = "ncftpput -m -c -u '{$this->user}' -p '{$this->pass}' -P {$this->port} {$this->host} {$this->path}$filename";

        return implode(' | ', $commands);
    }

    protected function buildFilename($filename, $gzip)
    {
        return date('Y-m-d') . "-" . time() . "-" . $filename . ($gzip ? ".gz" : "");
    }

    protected function run($command)
    {
        $process = Process::fromShellCommandline($command);
        $process->start();

        while ($process->isRunning()) {
            // waiting for process to finish
        }

        if ( ! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}