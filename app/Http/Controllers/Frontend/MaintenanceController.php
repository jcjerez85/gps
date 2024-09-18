<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use CustomFacades\ModalHelpers\ServiceModalHelper;
use CustomFacades\Repositories\DeviceServiceRepo;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Repositories\DeviceRepo;

class MaintenanceController extends Controller
{
    public function index($imei = null)
    {
        $services = [];
        $devices = [];

        if ($imei) {
            $this->checkException('checklist_qr_code', 'view');
            $device = DeviceRepo::whereImei($imei);
            $this->checkException('devices', 'show', $device);
        }

        if ($this->user->perm('maintenance', 'view')) {
            $devices = UserRepo::getDevicesWithServices($this->user->id, $imei);
        }

        foreach ($devices as $device) {
            foreach ($device->services as $service) {
                $services[] = $service;
            }
        }

        $data = [
            'services' => $services,
            'sorting'  => [
                'sort_by' => '',
                'sort'    => ''
            ],
        ];

        if (request()->ajax())
            return view('front::Maintenance.modal')->with($data);
        else
            return view('front::Maintenance.index')->with($data);
    }

    public function table() {
        $services = [];

        if ($this->user->perm('maintenance', 'view'))
        {
            $devices = UserRepo::getDevicesWithServices($this->user->id);

            foreach ($devices as $device)
            {
                foreach ($device->services as $service)
                {
                    $services[] = $service;
                }
            }
        }

        $sortBy = request()->input('sorting.sort_by', 'name');
        $sort   = request()->input('sorting.sort', 'desc') == 'asc' ? 'asc' : 'desc';

        $sortFunction = $sort == 'asc' ? 'sortBy' : 'sortByDesc';

        $services = collect($services)->{$sortFunction}(function ($service, $key) use($sortBy) {
            if ($sortBy == 'device.name')
                return $service->device->name;

            if ($sortBy == 'name')
                return $service->name;
            
            if ($service->expiration_by == 'odometer' && $sortBy != 'odometer_percentage' && $sortBy != 'odometer_left')
                return -1;

            if ($service->expiration_by == 'engine_hours' && $sortBy != 'engine_hours_percentage' && $sortBy != 'engine_hours_left')
                return -1;

            if ($service->expiration_by == 'days' && $sortBy != 'days_percentage' && $sortBy != 'days_left')
                return -1;

            switch ($sortBy) {
                case 'odometer_percentage':
                case 'engine_hours_percentage':
                case 'days_percentage':
                    return $service->percentage;

                case 'odometer_left':
                case 'engine_hours_left':
                case 'days_left':
                    return $service->left;
            }
        });

        $data = [
            'sorting'  => [
                'sort_by' => $sortBy,
                'sort'    => $sort
            ],
            'services' => $services
        ];

        return view('front::Maintenance.table')->with($data);
    }
}
