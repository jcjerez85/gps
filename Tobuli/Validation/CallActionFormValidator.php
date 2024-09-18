<?php
namespace Tobuli\Validation;

use Illuminate\Validation\Factory as IlluminateValidator;
use Tobuli\Entities\CallAction;

class CallActionFormValidator extends Validator
{
    public $rules = [
        'create' => [
            'device_id' => 'required|exists:devices,id',
            'event_id' => 'required|exists:events,id',
            'alert_id' => 'required|exists:alerts,id',
            'called_at' => 'required|date',
            'remarks' => 'required',
        ],
        'update' => [
            'device_id' => 'required|exists:devices,id',
            'event_id' => 'required|exists:events,id',
            'alert_id' => 'required|exists:alerts,id',
            'called_at' => 'required|date',
            'remarks' => 'required',
        ],
    ];

    public function validate($name, array $data, $id = NULL)
    {
        $this->rules[$name]['response_type'] = 'required|in:'.implode(',', array_column(CallAction::getResponseTypes(), 'type'));

        parent::validate($name, $data, $id);
    }
}
