<?php

namespace Tobuli\Reports\Reports;

use Tobuli\Reports\DeviceReport;

class ChecklistReport extends DeviceReport
{
    const TYPE_ID = 50;

    protected $formats = ['html', 'json'];

    public static function isEnabled()
    {
        if ( ! config('addon.checklists'))
            return false;

        $user = getActingUser();

        if ($user->perm('checklist', 'view'))
            return true;

        if ($user->perm('checklist_template', 'view'))
            return true;

        if ($user->perm('checklist_row_management', 'view'))
            return true;

        return false;
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.checklist_report');
    }

    protected function generateDevice($device)
    {
        $data = [];
        $services = $device
            ->services()
            ->with(['checklists' => function ($q) {
                    if ($this->parameters['status'] == 'complete') {
                        $q->complete();
                    } elseif ($this->parameters['status'] == 'incomplete') {
                        $q->incomplete();
                    } elseif ($this->parameters['status'] == 'failed') {
                        $q->failed();
                    }
                },
                'checklists.rows' => function ($q) {
                    if ($this->parameters['status'] == 'failed') {
                        $q->failed();
                    }
                },
                'checklists.rows.images',
            ])
            ->whereHas('checklists', function ($q) {
                if ($this->parameters['status'] == 'complete') {
                    $q->complete();
                } elseif ($this->parameters['status'] == 'incomplete') {
                    $q->incomplete();
                } elseif ($this->parameters['status'] == 'failed') {
                    $q->failed();
                }
            })
            ->get();

        foreach ($services as $service) {
            $data[] = [
                'service' => $service,
                'checklists' => $service->checklists,
            ];
        }

        return [
            'meta' => $this->getDeviceMeta($device),
            'data' => $data,
        ];
    }
}
