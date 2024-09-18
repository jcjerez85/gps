<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Transformers\Checklist\ChecklistFullTransformer;
use App\Transformers\Checklist\ChecklistImageFullTransformer;
use CustomFacades\Validators\ChecklistValidator;
use Illuminate\Support\Arr;
use Tobuli\Entities\Checklist;
use Tobuli\Entities\ChecklistRow;
use Tobuli\Entities\ChecklistTemplate;
use Tobuli\Entities\ChecklistImage;
use Tobuli\Entities\Device;
use Tobuli\Services\ChecklistService;
use Formatter;
use FractalTransformer;
use QrCode;

class ChecklistController extends BaseController
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

        $checklists = Checklist::with('rows')
            ->where('service_id', $service_id)
            ->orderBy('completed_at', 'asc')
            ->paginate(30);

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::paginate($checklists, ChecklistFullTransformer::class)->toArray()
        ));
    }

    public function store($service_id)
    {
        $this->checkException('checklist', 'store');
        $this->data['service_id'] = $service_id;
        ChecklistValidator::validate('create', $this->data);

        $this->checklistService->createChecklist($service_id, $this->data['template_id']);

        return response()->json(['status' => 1]);
    }

    public function destroy()
    {
        $item = Checklist::find($this->data['id']);

        $this->checkException('checklist', 'remove', $item);

        $item->delete();

        return response()->json(['status' => 1]);
    }

    public function upload($row_id)
    {
        $row = ChecklistRow::find($row_id);
        $this->checkException('checklist_activity', 'update', $row);

        if (isBase64($this->data['file'] ?? '')) {
            $this->data['file'] = base64ToImage($this->data['file']);
        }

        ChecklistValidator::validate('upload', $this->data);
        $image = $this->checklistService->saveRowFile($row, $this->data['file']);

        return response()->json(array_merge(
            ['status' => 1],
            FractalTransformer::item($image, ChecklistImageFullTransformer::class)->toArray()
        ));
    }

    public function updateRowStatus($row_id)
    {
        $row = ChecklistRow::find($row_id);

        $this->checkException('checklist_activity', 'update', $row);

        try {
            $this->checklistService->validateUpdateRowStatus($row, $this->data['completed']);
        } catch (\Exception $exception) {
            return response()->json(['status' => 0, 'error' => $exception->getMessage()], 400);
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

        if ( ! $this->checklistService->canSignChecklist($item, $this->data['signature']))
            return response()->json(['status' => 0, 'error' => trans('front.incomplete_checklist')], 400);

        $this->checklistService->setChecklistNotes($item, Arr::get($this->data, 'notes'));
        $this->checklistService->signChecklist($item, $this->data['signature']);

        return response()->json(['status' => 1, 'completed_at' => Formatter::time()->human($item->completed_at)]);
    }

    public function deleteFile($row_id)
    {
        ChecklistValidator::validate('delete_file', $this->data);
        $row = ChecklistRow::find($row_id);

        $this->checkException('checklist_activity', 'update', $row);

        $path = $this->checklistService->getRowFilesPath($row).'/'.$this->data['filename'];

        $image = ChecklistImage::where([
            'row_id' => $row->id,
            'path' => $path,
            'checklist_history_id' => null,
        ])->first();

        if (! $image || ! $this->checklistService->deleteImage($image)) {
            throw new ResourseNotFoundException(trans('validation.attributs.file'));
        }

        return response()->json(['status' => 1]);
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

        return response()->json(['status' => 1]);
    }

    public function getTypes()
    {
        return response()->json([
            'status' => 1,
            'types' => toOptions(ChecklistTemplate::getTypes()),
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

    public function getCompleted()
    {
        $checklists = Checklist::complete()
            ->byUser($this->user)
            ->paginate(30);

        return response()->json(array_merge(
                ['status' => 1],
                FractalTransformer::setIncludes(['driver'])
                    ->paginate($checklists, ChecklistFullTransformer::class)
                    ->toArray()
            ));
    }

    public function getFailed()
    {
        $checklists = Checklist::with(['rows' => function($q) {
                $q->failed();
            }])
            ->failed()
            ->byUser($this->user)
            ->paginate(30);

        return response()->json(array_merge(
                ['status' => 1],
                FractalTransformer::setIncludes(['rows'])
                    ->paginate($checklists, ChecklistFullTransformer::class)
                    ->toArray()
            ));
    }
}
