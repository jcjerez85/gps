<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class KeyCreateCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'key:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application key if not exsist';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->hasKey())
            return;

        $this->setKeyRow();

        $this->call('key:generate', ['--force' => true]);
    }

    protected function hasKey()
    {
        return preg_match('/^APP_KEY=/m', file_get_contents($this->environmentFilePath()));
    }

    protected function setKeyRow()
    {
        $file = $this->environmentFilePath();

        $content = file_get_contents($file);

        if (!$content)
            throw new \Exception('Environment file content empty');

        $content = "APP_KEY={$this->laravel['config']['app.key']}\n" . $content;

        file_put_contents($file, $content);
    }

    protected function environmentFilePath()
    {
        return base_path('.env');
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