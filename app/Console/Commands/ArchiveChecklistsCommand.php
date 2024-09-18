<?php namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Tobuli\Entities\Checklist;
use Tobuli\Entities\Timezone;
use Tobuli\Services\ChecklistService;

class ArchiveChecklistsCommand extends Command {
    protected $name = 'checklists:archive';
    protected $description = 'Archives checklists of last day.';

    private $checklistService;

    public function __construct(ChecklistService $checklistService)
    {
        $this->checklistService = $checklistService;

        parent::__construct();
    }

    public function handle()
    {
        $tz = Timezone::find(settings('main_settings.default_timezone'));

        $time = Carbon::now($tz->hi_format)
            ->startOfDay()
            ->tz('UTC');

        $checklists = Checklist::whereNotNull('completed_at')
            ->where('completed_at', '<', $time)
            ->orWhereHas('rows', function($q) use ($time) {
                $q->where('completed_at', '<', $time);
            })
            ->orWhereHas('rows.images', function($q) use ($time) {
                $q->where('created_at', '<', $time);
            })
            ->get();

        foreach ($checklists as $checklist) {
            $this->checklistService->archiveChecklist($checklist);
        }

        $this->line('Ok');
    }
}
