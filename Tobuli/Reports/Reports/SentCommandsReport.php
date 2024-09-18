<?php

namespace Tobuli\Reports\Reports;

use Tobuli\Entities\SentCommand;
use Tobuli\Reports\DeviceReport;
use Formatter;

class SentCommandsReport extends DeviceReport
{
    const TYPE_ID = 32;
    const STATUS_FAIL = 'fail';
    const STATUS_SENT = 'sent';

    protected $enableFields = ['devices', 'metas'];

    public function getInputParameters(): array
    {
        return [
            \Field::select('status', trans('validation.attributes.status'), 1)
                ->setOptions([
                    '' => trans('front.none'),
                    SentCommandsReport::STATUS_FAIL => SentCommandsReport::STATUS_FAIL,
                    SentCommandsReport::STATUS_SENT => SentCommandsReport::STATUS_SENT,
                ])
                ->setValidation('in:' . SentCommandsReport::STATUS_SENT . ',' . SentCommandsReport::STATUS_FAIL)
        ];
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.sent_commands');
    }

    protected function defaultMetas()
    {
        return array_merge(parent::defaultMetas(), [
            'device.imei' => trans('validation.attributes.imei'),
        ]);
    }

    protected function beforeGenerate()
    {
        $this->totals['is_manager'] = $this->user->isAdmin() || $this->user->isManager();
    }

    protected function generateDevice($device)
    {
        $query =  SentCommand::with(['user', 'template'])
            ->where('device_imei', $device->imei)
            ->whereBetween('created_at', [$this->date_from, $this->date_to]);

        if ( ! empty($this->parameters['status']))
            $query->where('status', $this->parameters['status'] == self::STATUS_SENT ? 1 : 0);

        $commands = $query->get();

        $user = $this->user;

        $commands = $commands->filter(function ($command) use ($user) {
            if (is_null($sender = $command->user))
                return false;

            return $user->can('view', $sender);
        });

        $results = [];

        foreach ($commands as $command) {
            $results[] = [
                'email'      => $command->user->email,
                'connection' => $command->connection,
                'command'    => $command->command_title,
                'time'       => Formatter::time()->human($command->created_at),
                'status'     => $command->status ? self::STATUS_SENT : self::STATUS_FAIL,
            ];
        }

        return [
            'meta' => $this->getDeviceMeta($device),
            'data' => [
                'commands' => $results,
            ],
        ];
    }
}