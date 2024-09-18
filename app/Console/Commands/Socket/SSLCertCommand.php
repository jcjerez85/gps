<?php namespace App\Console\Commands\Socket;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class SSLCertCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'socket:ssl';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Parse and symlink SSL cert.';


	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        if ( ! File::exists('/var/www/html/private.key') && $path = $this->search("SSLCertificateKeyFile"))
            $this->symlink($path, "/var/www/html/private.key");

        if ( ! File::exists('/var/www/html/cert.crt') && $path = $this->search("SSLCertificateFile"))
            $this->symlink($path, "/var/www/html/cert.crt");

        if ( ! File::exists('/var/www/html/cert_ca.crt')) {
            if ($path = $this->search("SSLCACertificateFile"))
                $this->symlink($path, "/var/www/html/cert_ca.crt");
            elseif ($path = $this->search("SSLCertificateChainFile "))
                $this->symlink($path, "/var/www/html/cert_ca.crt");
        }

        $this->line('OK');
	}

	protected function search($name) {
	    $process = Process::fromShellCommandline("grep -r --include=\*.conf '^[^#;]*$name' /etc/httpd/");
        $process->run();

        while ($process->isRunning()) {}

        $output = $process->getOutput();

        if ( ! $output)
            return false;

        $lines = explode("\n", $output);
        list($conf, $path) = explode(' ', $lines[0], 2);

        return $path;
    }

    protected function symlink($source, $target)
    {
        $process = Process::fromShellCommandline("ln -sf $source $target");
        $process->run();

        while ($process->isRunning()) {}

        if ( ! $process->isSuccessful())
            throw new ProcessFailedException($process);

        return true;
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
