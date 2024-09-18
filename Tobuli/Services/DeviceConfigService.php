<?php

namespace Tobuli\Services;

use App\Exceptions\ResourseNotFoundException;
use App\Jobs\SendConfigurationCommands;
use CustomFacades\Server;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\SMS\Services\SendSmsManager;
use Validator;

class DeviceConfigService
{
    private $placeholders;
    private $fieldValidationRules;
    private $defaultData;
    private $smsManager;

    public function __construct()
    {
        $this->placeholders = [
            'apn_name' => '%APNNAME%',
            'apn_username' => '%APNUSERNAME%',
            'apn_password' => '%APNPASSWORD%',
            'ip' => '%IP%',
        ];

        $this->fieldValidationRules = [
            'apn_name' => 'required|string|max:255',
            'apn_username' => 'string|max:255',
            'apn_password' => 'string|max:255',
            'ip' => 'required|ip',
        ];

        $this->defaultData = [
            'ip' => Server::ip(),
        ];
    }

    public function setSmsManager($smsManager)
    {
        if ($smsManager instanceof SendSmsManager) {
            $this->smsManager = $smsManager;
        }

        if (! $this->smsManager) {
            throw new ResourseNotFoundException(trans('front.sms_gateway'));
        }

        return $this;
    }

    public function getSmsManager()
    {
        return $this->smsManager;
    }

    public function configureDevice($phoneNumber, $data, $commands)
    {
        if (! $this->smsManager) {
            throw new ResourseNotFoundException(trans('front.sms_gateway'));
        }

        dispatch(new SendConfigurationCommands(
            $this->smsManager,
            $phoneNumber,
            $this->prepareCommands($commands, $this->mergeDefaultData($data))
        ));

        return true;
    }

    private function mergeDefaultData($data)
    {
        return array_merge($this->defaultData, $data);
    }

    private function prepareCommands($commands, $data)
    {
        return array_map(function($command) use($data) {
                return $this->prepareCommand($command, $data);
            },
            $commands);
    }

    private function prepareCommand($command, $data)
    {
        $placeholders = $this->getCommandPlaceholders($command);

        if (empty($placeholders)) {
            return $command;
        }

        $this->validateCommandValues($placeholders, $data);

        $result = $command;

        foreach ($placeholders as $fieldName => $placeholder) {
            $result = str_replace($placeholder, $data[$fieldName] ?? '', $result);
        }

        return $result;
    }

    private function getCommandPlaceholders($command)
    {
        return array_filter($this->placeholders, function($placeholder) use ($command) {
            return strpos($command, $placeholder) !== false;
        });
    }

    private function validateCommandValues($placeholders, $values)
    {
        $rules = $this->getValidationRules($placeholders);

        if (empty($rules)) {
            return;
        }

        $validator = Validator::make($values, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }
    }

    private function getValidationRules($placeholders)
    {
        return array_filter($this->fieldValidationRules, function($key) use($placeholders) {
            return isset($placeholders[$key]);
        }, ARRAY_FILTER_USE_KEY);
    }
}
