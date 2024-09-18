<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Transformers\Sharing\SharingFullTransformer;
use CustomFacades\Validators\SharingFormValidator;
use Formatter;
use FractalTransformer;
use Tobuli\Entities\Device;
use Tobuli\Entities\Sharing;
use Tobuli\Services\SharingService;

class SharingController extends BaseController
{
    private $sharingService;

    public function __construct(SharingService $sharingService)
    {
        parent::__construct();

        $this->sharingService = $sharingService;
    }

    public function index()
    {
        $this->checkException('sharing', 'view');

        $paginator = Sharing::where('user_id', $this->user->id)
            ->filter($this->data)
            ->paginate();

        return FractalTransformer::paginate($paginator, SharingFullTransformer::class)
            ->toArray();
    }

    public function show(int $id)
    {
        /** @var Sharing $entity */
        $entity = Sharing::findOrFail($id);

        $this->checkException('sharing', 'show', $entity);

        return $this->transformEntity($entity);
    }

    private function transformEntity(Sharing $entity)
    {
        return FractalTransformer::item($entity, SharingFullTransformer::class)->toArray();
    }

    public function update(int $id)
    {
        /** @var Sharing $entity */
        $entity = Sharing::findOrFail($id);

        $this->checkException('sharing', 'update', $entity);

        SharingFormValidator::validate('update_api', $this->data);

        $this->normalize($this->data);

        $this->sharingService->update($entity, $this->data);

        if (isset($this->data['devices'])) {
            $devices = Device::whereIn('id', $this->data['devices'])->filterUserAbility($this->user);

            $this->sharingService->syncDevices($entity, $devices);
        }

        return $this->transformEntity($entity);
    }

    public function updateDevices(int $id)
    {
        /** @var Sharing $entity */
        $entity = Sharing::findOrFail($id);

        $this->checkException('sharing', 'update', $entity);

        SharingFormValidator::validate('update_devices_api', $this->data);

        $devices = Device::whereIn('id', $this->data['devices'])->filterUserAbility($this->user);

        $this->sharingService->syncDevices($entity, $devices);

        return $this->transformEntity($entity);
    }

    public function store()
    {
        $this->checkException('sharing', 'store');

        SharingFormValidator::validate('create_api', $this->data);

        $this->normalize($this->data);

        $entity = $this->sharingService->create($this->user->id, $this->data);

        if (isset($this->data['devices'])) {
            $devices = Device::whereIn('id', $this->data['devices'])->filterUserAbility($this->user);

            $this->sharingService->syncDevices($entity, $devices);
        }

        return $this->transformEntity($entity);
    }

    public function delete(int $id)
    {
        /** @var Sharing $entity */
        $entity = Sharing::findOrFail($id);

        $this->checkException('sharing', 'remove', $entity);

        $this->sharingService->remove($entity);

        return ['status' => 1];
    }

    private function normalize(&$data)
    {
        if (isset($data['expiration_date'])) {
            $data['expiration_date'] = Formatter::time()->reverse($data['expiration_date']);
        }
    }
}
