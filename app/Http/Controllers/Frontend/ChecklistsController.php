<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use CustomFacades\Validators\ChecklistValidator;
use Illuminate\Support\Arr;
use Tobuli\Entities\Checklist;
use Tobuli\Entities\ChecklistRow;
use Tobuli\Entities\ChecklistTemplate;
use Tobuli\Entities\ChecklistImage;
use Tobuli\Entities\Device;
use Tobuli\Services\ChecklistService;
use Formatter;
use QrCode;

class ChecklistsController extends Controller
{
    private $checklistService;

    public function __construct(ChecklistService $checklistService)
    {
        $this->checklistService = $checklistService;

        parent::__construct();
    }

    public function index($service_id)
    {
        $this->checkException('checklist', 'view');

        $data = Checklist::with('rows')
            ->where('service_id', $service_id)
            ->orderBy('completed_at', 'asc')
            ->paginate(15);

        return view('front::Checklist.index')
            ->with([
                'checklists' => $data,
                'service_id' => $service_id,
            ]);
    }

    public function table($service_id)
    {
        $this->checkException('checklist', 'view');

        $data =  Checklist::with('rows')
            ->where('service_id', $service_id)
            ->orderBy('completed_at', 'asc')
            ->paginate(15);

        return view('front::Checklist.table')
            ->with([
                'checklists' => $data,
                'service_id' => $service_id,
            ]);
    }

    public function create($service_id)
    {
        $this->checkException('checklist', 'create');

        $templates = ChecklistTemplate::available()
            ->get()
            ->pluck('name', 'id');

        return view('front::Checklist.create', [
            'service_id' => $service_id,
            'templates' => $templates,
        ]);
    }

    public function store($service_id)
    {
        $this->checkException('checklist', 'store');
        $this->data['service_id'] = $service_id;
        ChecklistValidator::validate('create', $this->data);

        $this->checklistService->createChecklist($service_id, $this->data['template_id']);

        return ['status' => 1];
    }

    public function doDestroy($checklist_id)
    {
        $item = Checklist::find($checklist_id);

        $this->checkException('checklist', 'remove', $item);

        return view('front::Checklist.destroy')->with([
            'id' => $checklist_id,
        ]);
    }

    public function destroy()
    {
        $item = Checklist::find($this->data['id']);

        $this->checkException('checklist', 'remove', $item);

        $item->delete();

        return ['status' => 1];
    }

    public function upload($row_id)
    {
        $row = ChecklistRow::find($row_id);
        $this->checkException('checklist_activity', 'update', $row);
        ChecklistValidator::validate('upload', $this->data);

        $this->checklistService->saveRowFile($row, $this->data['file']);

        return ['status' => 1];
    }

    public function updateRowStatus($row_id)
    {
        $row = ChecklistRow::find($row_id);

        $this->checkException('checklist_activity', 'update', $row);

        try {
            $this->checklistService->validateUpdateRowStatus($row, $this->data['completed']);
        } catch (\Exception $exception) {
            return response()->json(['status' => 0, 'error' => $exception->getMessage()]);
        }

        $this->checklistService->updateRowStatus($row, $this->data['completed']);

        return response()->json(['status' => 1]);
    }

    public function updateRowOutcome($row_id)
    {
        $row = ChecklistRow::find($row_id);

        $this->checkException('checklist_activity', 'update', $row);

        ChecklistValidator::validate('outcome', $this->data);

        try {
            $this->checklistService->validateUpdateRowOutcome($row, $this->data['outcome']);
        } catch (\Exception $exception) {
            return response()->json(['status' => 0, 'error' => $exception->getMessage()], 400);
        }

        $this->checklistService->updateRowOutcome($row, $this->data['outcome']);

        return response()->json(['status' => 1]);
    }

    public function sign($checklist_id)
    {
        $item = Checklist::find($checklist_id);

        $this->checkException('checklist', 'update', $item);
        ChecklistValidator::validate('sign', $this->data); //@TODO: check why thrown exception doesn't contain any message

        if (! $this->checklistService->canSignChecklist($item, $this->data['signature'])) {
            return ['status' => 0, 'error' => trans('front.incomplete_checklist')];
        }

        $this->checklistService->setChecklistNotes($item, Arr::get($this->data, 'notes'));
        $this->checklistService->signChecklist($item, $this->data['signature']);

        return ['status' => 1, 'completed_at' => Formatter::time()->human($item->completed_at)];
    }

    public function deleteImage($image_id)
    {
        $image = ChecklistImage::find($image_id);

        if (! $image || $image->checklist_history_id) {
            throw new ResourseNotFoundException(trans('validation.attributes.file'));
        }

        $row = $image->checklistRow;

        $this->checkException('checklist_activity', 'update', $row);

        if (! $this->checklistService->deleteImage($image)) {
            throw new ResourseNotFoundException(trans('validation.attributes.file'));
        }

        return ['status' => 1];
    }

    public function getChecklists($service_id)
    {
        $this->checkException('checklist', 'view');
        $checklists = [];

        if ($this->user->perm('maintenance', 'view')) {
            $query = Checklist::with('rows')
                ->where('service_id', $service_id)
                ->orderBy('completed_at', 'asc');

            if ($this->user->perm('checklist_qr_pre_start_only', 'view')) {
                $query->where('type', ChecklistTemplate::TYPE_PRE_START);
            }

            $checklists = $query->get();
        }

        return view('front::Checklist.partials.list')->with([
            'checklists' => $checklists,
        ]);
    }

    public function getRow($row_id)
    {
        $row = ChecklistRow::find($row_id);
        $this->checkException('checklist_activity', 'view');

        if ($row->checklist->type == Checklist::TYPE_PRE_START)
            $view = 'front::Checklist.partials.prestart_row';
        else
            $view = 'front::Checklist.partials.service_form';

        return view($view)->with([
            'row' => $row,
        ]);
    }

    public function preview($checklist_id)
    {
        $checklist = Checklist::find($checklist_id);
        $this->checkException('checklist', 'show', $checklist);

        return view('front::Checklist.preview')
            ->with([
                'checklist' => $checklist,
            ]);
    }

    public function edit($checklist_id)
    {
        $checklist = Checklist::find($checklist_id);
        $this->checkException('checklist', 'edit', $checklist);

        return view('front::Checklist.edit')
            ->with([
                'item' => $checklist,
            ]);
    }

    public function qrCode($device_id)
    {
        $device = Device::find($device_id);
        $this->checkException('devices', 'show', $device);
        $this->checkException('checklist_qr_code', 'view');

        $maintenanceUrl = route('maintenance.index', ['imei' => $device->imei]);

        return view('front::Checklist.qr_code')
            ->with([
                'device_id' => $device_id,
                'maintenanceUrl' => $maintenanceUrl,
                'downloadUrl' => route('checklist.qr_code_download', $device_id),
            ]);
    }

    public function qrCodeImage($device_id)
    {
        $device = Device::find($device_id);
        $this->checkException('devices', 'show', $device);
        $this->checkException('checklist_qr_code', 'view');

        $maintenanceUrl = route('maintenance.index', ['imei' => $device->imei]);
        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($maintenanceUrl);

        return response($qrCode, 200)
            ->header("Content-Type", 'image/png');
    }

    public function downloadQrCode($device_id)
    {
        $device = Device::find($device_id);
        $this->checkException('devices', 'show', $device);
        $this->checkException('checklist_qr_code', 'view');

        $path = public_path("images/{$device->imei}.png");
        QrCode::format('png')
            ->size(300)
            ->generate(route('maintenance.index', ['imei' => $device->imei]), $path);

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
