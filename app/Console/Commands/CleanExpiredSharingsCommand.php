<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tobuli\Entities\Sharing;

class CleanExpiredSharingsCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sharing:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired sharing links';

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
            Sharing::where('delete_after_expiration', 1)
                ->expired()
                ->delete();

            $this->line('Ok');
        } catch(\Exception $e) {

        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
