<?php namespace Tobuli\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tobuli\Entities\Checklist;
use Tobuli\Entities\ChecklistHistory;
use Tobuli\Entities\ChecklistRow;
use Tobuli\Entities\ChecklistRowHistory;
use Tobuli\Entities\ChecklistTemplate;
use Tobuli\Entities\ChecklistImage;
use Tobuli\Entities\UserDriver;
use Tobuli\Exceptions\ValidationException;

class ChecklistService
{
    public function createChecklist($serviceId, $templateId)
    {
        $template = ChecklistTemplate::find($templateId);

        if (! $template) {
            return null;
        }

        $checklist = new Checklist();
        $checklist->template_id = $templateId;
        $checklist->service_id = $serviceId;
        $checklist->name = $template->name;
        $checklist->type = $template->type;

        $checklist->save();
        $this->copyTemplateRows($checklist, $template);

        return $checklist;
    }

    public function copyTemplateRows(Checklist $checklist, $template)
    {
        $templateRows = $template->rows()->get()->toArray();
        $rows = array_map(function($data) use($checklist) {
            return [
                'checklist_id' => $checklist->id,
                'template_row_id' => $data['id'],
                'activity' => $data['activity'],
            ];
        }, $templateRows);

        ChecklistRow::insert($rows);
    }

    public function canUpdateRowStatus(ChecklistRow $row, $completed)
    {
        if ( ! $completed) {
            return true;
        }

        $user = getActingUser();

        if ($user && $user->perm('checklist_optional_image', 'view'))
            return true;

        return $row->images->count();
    }

    public function validateUpdateRowStatus(ChecklistRow $row, $completed)
    {
        if ( ! $completed) {
            return true;
        }

        $user = getActingUser();

        if ($user && !$user->perm('checklist_optional_image', 'view') && !$row->images->count())
            throw new \Exception(trans('front.please_upload_image'));

        if (is_null($row->outcome))
            throw new \Exception(trans('front.activity_outcome_required'));

        return true;
    }

    public function updateRowStatus(ChecklistRow $row, $completed)
    {
        $row->completed = $completed;
        $row->completed_at = $completed ? date('Y-m-d H:i:s') : null;
        $row->save();

        if (! $completed) {
            $this->signChecklist($row->checklist, null);
        }

        return true;
    }

    public function validateUpdateRowOutcome(ChecklistRow $row, $outcome)
    {
        return true;
    }

    public function updateRowOutcome(ChecklistRow $row, $outcome)
    {
        $row->outcome = $outcome;
        $row->save();

        if ($row->checklist->type == Checklist::TYPE_PRE_START)
            $this->updateRowStatus($row, true);
    }

    public function saveRowFile(ChecklistRow &$row, $file)
    {
        if ($row->images->count() >= 4) {
            throw new ValidationException(trans('validation.array_max', [
                'attribute' => trans('front.photo'),
                'max' => 4, //@TODO: move to settings?
            ]));
        }

        $path = $this->getRowFilesPath($row);

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filename = $this->generateFilename($path, $file->getClientOriginalExtension());
        $photoPath = "{$path}/{$filename}";

        $file->move(public_path($path), $filename);

        return $row->saveImage($photoPath);
    }

    public function canSignChecklist(Checklist $checklist, $signature = null)
    {
        if (! $signature && ! $checklist->completed_at) {
            return true;
        }

        $incompleteRows = $checklist->incompleteRows();

        if ($incompleteRows->count() && ! empty($signature)) {
            return false;
        }

        return true;
    }

    public function setChecklistNotes(Checklist &$checklist, $notes)
    {
        $checklist->notes = $notes;
        $checklist->save();
    }

    public function signChecklist(Checklist &$checklist, $signature = null)
    {
        $checklist->signature = $signature;
        $checklist->completed_at = $signature ? date('Y-m-d H:i:s') : null;
        $checklist->save();

        if ($checklist->type == Checklist::TYPE_PRE_START) {
            $this->assignDriver($checklist);
        }
    }

    public function deleteImage(ChecklistImage $image)
    {
        $row = $image->checklistRow;
        $image->delete();

        if (! $row->images()->count()) {
            $this->updateRowStatus($row, false);
        }

        return true;
    }

    public function getRowFilesPath($row)
    {
        return "images/checklistPhotos/{$row->checklist()->first()->id}/{$row->id}";
    }

    public function archiveChecklist(Checklist $checklist)
    {
        $checklistHistory = new ChecklistHistory([
            'template_id' => $checklist->template_id,
            'checklist_id' => $checklist->id,
            'service_id' => $checklist->service_id,
            'name' => $checklist->name,
            'signature' => $checklist->signature,
            'completed_at' => $checklist->completed_at,
            'notes' => $checklist->notes,
        ]);

        $checklistHistory->save();

        $this->archiveRows($checklist, $checklistHistory);
        $this->resetChecklist($checklist);
        $this->archiveImages($checklist, $checklistHistory);
    }

    private function archiveRows(Checklist $checklist, ChecklistHistory $checklistHistory)
    {
        $data = [];

        foreach ($checklist->rows as $row) {
            $data[] = [
                'checklist_history_id' => $checklistHistory->id,
                'checklist_id' => $checklist->id,
                'checklist_row_id' => $row->id,
                'template_row_id' => $row->template_row_id,
                'activity' => $row->activity,
                'completed' => $row->completed,
                'completed_at' => $row->completed_at,
                'outcome' => $row->outcome,
            ];
        }

        if ($data) {
            ChecklistRowHistory::insert($data);
        }
    }

    private function resetChecklist(Checklist $checklist)
    {
        $checklist->notes = null;
        $checklist->signature = null;
        $checklist->completed_at = null;
        $checklist->save();

        ChecklistRow::where('checklist_id', $checklist->id)
            ->update([
                'outcome' => null,
                'completed' => 0,
                'completed_at' => null,
            ]);
    }

    private function archiveImages(Checklist $checklist, ChecklistHistory $checklistHistory)
    {
        ChecklistImage::where('checklist_id' , $checklist->id)
            ->whereNull('checklist_history_id')
            ->update([
                'checklist_history_id' => $checklistHistory->id,
            ]);
    }

    private function generateFilename($path, $extension)
    {
        do {
            $filename = uniqid().'.'.$extension;
        } while(File::exists("{$path}/{$filename}"));

        return $filename;
    }

    private function assignDriver(Checklist $checklist)
    {
        $device = $checklist->service->device;
        $user = getActingUser();

        if (! $device || !$user) {
            return;
        }

        $userDriver = $this->getDriver($user);

        if (! $userDriver) {
            return;
        }

        if ($device->current_driver_id == $userDriver->id) {
            return;
        }

        $device->changeDriver($userDriver);
    }

    private function getDriver(\Tobuli\Entities\User $user)
    {
        $userDriver = UserDriver::where('email', $user->email)
            ->first();

        if ($userDriver) {
            return $userDriver;
        }

        $modalHelper = new \ModalHelpers\UserDriverModalHelper();
        $modalHelper->setData([
            'name' => $user->email,
            'email' => $user->email,
            'current' => 1,
        ]);
        $data = $modalHelper->create();

        return $data['item'] ?? null;
    }
}
