<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tobuli\Services\TranslationService;

class ServerTranslationsCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'server:translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates server translations.';

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
    public function handle()
    {
        try {
            $translationService = new TranslationService();
            $translationService->updateTranslationFiles();

            $this->line('Ok');
        }
        catch (\Exception $e) {
        }

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
