<?php namespace ModalHelpers;

use CustomFacades\Repositories\TrackerPortRepo;
use CustomFacades\Repositories\UserGprsTemplateRepo;
use CustomFacades\Validators\UserGprsTemplateFormValidator;
use Illuminate\Support\Arr;
use Tobuli\Entities\DeviceType;
use Tobuli\Entities\UserGprsTemplate;

class UserGprsTemplateModalHelper extends ModalHelper
{
    public function __construct()
    {
        parent::__construct();

        $this->adaptedies = UserGprsTemplate::getAdapties();

        if (!$this->user->perm('device.protocol', 'view'))
            unset($this->adaptedies['protocol']);

        if (!$this->user->perm('devices', 'view'))
            unset($this->adaptedies['devices']);

        if (!$this->user->perm('device.device_type_id', 'view'))
            unset($this->adaptedies['device_types']);
    }

    public function get()
    {
        $this->checkException('user_gprs_templates', 'view');

        $user_gprs_templates = UserGprsTemplate::userOwned($this->user)
            ->search($this->data['search_phrase'] ?? null)
            ->filter($this->data)
            ->toPaginator(10, 'id', 'desc');

        if ($this->api) {
            $user_gprs_templates = $user_gprs_templates->toArray();
            $user_gprs_templates['url'] = route('api.get_user_gprs_templates');
        }

        return compact('user_gprs_templates');
    }

    public function createData()
    {
        $this->checkException('user_gprs_templates', 'create');

        $protocols = TrackerPortRepo::getProtocolList();

        $adaptedies = $this->adaptedies;
        $devices = $this->user->devices;
        $device_types = DeviceType::active()->get()->pluck('title', 'id');

        if ($this->api) {
            $adaptedies = apiArray($adaptedies);
            $protocols = apiArray($protocols);
            $devices = apiArray($devices->pluck('name', 'id')->all());
            $device_types = apiArray($device_types->all());
        }

        return compact('adaptedies', 'protocols', 'devices', 'device_types');
    }

    public function create()
    {
        $this->checkException('user_gprs_templates', 'store');

        UserGprsTemplateFormValidator::validate('create', $this->data);

        $item = UserGprsTemplateRepo::create([
            'user_id' => $this->user->id,
            'title' => Arr::get($this->data, 'title'),
            'adapted' => Arr::get($this->data, 'adapted'),
            'protocol' => Arr::get($this->data, 'protocol'),
            'message' => Arr::get($this->data, 'message')
        ]);

        $item->devices()->sync(Arr::get($this->data, 'devices', []));
        $item->deviceTypes()->sync(Arr::get($this->data, 'device_types', []));

        return ['status' => 1, 'item' => $item];
    }

    public function editData()
    {
        $id = array_key_exists('user_gprs_template_id', $this->data) ? $this->data['user_gprs_template_id'] : request()->route('user_gprs_templates');
        
        $item = UserGprsTemplateRepo::find($id);

        $this->checkException('user_gprs_templates', 'edit', $item);

        $item->load("devices:id", "deviceTypes:id");

        $protocols = TrackerPortRepo::getProtocolList();

        $adaptedies = $this->adaptedies;
        $devices = $this->user->devices;
        $device_types = DeviceType::active()->get()->pluck('title', 'id');

        if ($this->api) {
            $adaptedies = apiArray($adaptedies);
            $protocols = apiArray($protocols);
            $devices = apiArray($devices->pluck('name', 'id')->all());
            $device_types = apiArray($device_types->all());
        }

        return compact('item','adaptedies', 'protocols', 'devices', 'device_types');
    }

    public function edit()
    {
        $item = UserGprsTemplateRepo::find($this->data['id']);

        $this->checkException('user_gprs_templates', 'update', $item);

        UserGprsTemplateFormValidator::validate('update', $this->data);

        UserGprsTemplateRepo::update($item->id, [
            'title' => Arr::get($this->data, 'title'),
            'adapted' => Arr::get($this->data, 'adapted'),
            'protocol' => Arr::get($this->data, 'protocol'),
            'message' => Arr::get($this->data, 'message')
        ]);

        $item->devices()->sync(Arr::get($this->data, 'devices', []));
        $item->deviceTypes()->sync(Arr::get($this->data, 'device_types', []));

        return ['status' => 1];
    }

    public function getMessage()
    {
        $id = array_key_exists('user_gprs_template_id', $this->data) ? $this->data['user_gprs_template_id'] : $this->data['id'];
        
        $item = UserGprsTemplateRepo::find($id);

        $this->checkException('user_gprs_templates', 'show', $item);

        return ['status' => 1, 'message' => $item->message];
    }

    public function doDestroy($id)
    {
        $item = UserGprsTemplateRepo::find($id);

        $this->checkException('user_gprs_templates', 'remove', $item);

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('user_gprs_template_id', $this->data) ? $this->data['user_gprs_template_id'] : $this->data['id'];
        
        $item = UserGprsTemplateRepo::find($id);

        $this->checkException('user_gprs_templates', 'remove', $item);

        UserGprsTemplateRepo::delete($id);
        
        return ['status' => 1];
    }
}