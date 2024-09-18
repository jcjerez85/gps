<?php

namespace Tobuli\Helpers\SMS;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\SMS\Services\SendSmsPlivo;
use Tobuli\Helpers\SMS\Services\HTTP\SendSmsGET;
use Tobuli\Helpers\SMS\Services\HTTP\SendSmsPOST;
use Tobuli\Helpers\SMS\Services\SendSmsApp;

class SMSGatewayManager
{
    /**
     * @param User $user
     * @param null $test_args
     * @return mixed
     * @throws ValidationException
     */
    public function loadSender(User $user, $gateway_args = null)
    {
        if (is_null($gateway_args))
            $gateway_args = $this->getGatewayArguments($user);

        if (empty($gateway_args['request_method']))
            throw new ValidationException(['sender_service' => trans('validation.sms_gateway_error')]);

        switch ($gateway_args['request_method']) {
            case 'get':
                $sender = SendSmsGET::class;
                break;
            case 'post':
                $sender = SendSmsPOST::class;
                break;
            case 'plivo':
                $sender = SendSmsPlivo::class;
                break;
            case 'app':
                $sender = SendSmsApp::class;
                break;
            case 'server':
                $settings = settings('sms_gateway');

                if (empty($settings['enabled']))
                    throw new ValidationException(['sender_service' => trans('validation.sms_gateway_error')]);

                if ($settings['request_method'] == 'app' && !$this->getUser($settings['user_id'])) {
                    throw new ValidationException(['sender_service' => trans('validation.sms_gateway_error')]);
                }

                return $this->loadSender($user, $settings);
            case 'system':
                $settings = settings('sms_gateway');

                return $this->loadSender($user, $settings);
            default:
                throw new ValidationException(['sender_service' => trans('validation.sms_gateway_error')]);
        }

        return new $sender($gateway_args);
    }

    /**
     * @param $user
     * @param $test_args
     * @return mixed
     */
    private function getGatewayArguments($user)
    {
        return $this->getUserGatewayArgs($user);
    }

    /**
     * @param $user
     * @return mixed
     */
    protected function getUserGatewayArgs($user)
    {
        $gateway_args = $user->sms_gateway_params;
        $gateway_args['sms_gateway_status'] = $user->sms_gateway;
        $gateway_args['sms_gateway_url'] = $user->sms_gateway_url;
        $gateway_args['user_id'] = $user->id;

        return $gateway_args;
    }

    /**
     * @param $user_id
     * @return null|User
     */
    private function getUser($user_id)
    {
        if (is_null($user_id))
            return null;

        return Cache::store('array')->rememberForever("user.$user_id", function() use ($user_id) {
            return User::find($user_id);
        });
    }
}