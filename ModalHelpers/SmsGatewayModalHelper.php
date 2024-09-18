<?php namespace ModalHelpers;

use CustomFacades\Repositories\SmsEventQueueRepo;
use CustomFacades\Validators\SendTestSmsFormValidator;
use CustomFacades\Validators\SMSGatewayFormValidator;
use Tobuli\Helpers\SMS\SMSGatewayManager;


class SmsGatewayModalHelper extends ModalHelper
{
    public function sendTestSms()
    {
        $test_sms_gateway_args = $this->data;
        $test_sms_gateway_args['user_id'] = $this->user->id;

        SMSGatewayFormValidator::validate($test_sms_gateway_args['request_method'], $test_sms_gateway_args);
        SendTestSmsFormValidator::validate('create', $test_sms_gateway_args);

        $sms_manager = new SMSGatewayManager();
        $sms_sender_service = $sms_manager->loadSender($this->user, $test_sms_gateway_args);

        $sms_sender_service->send($test_sms_gateway_args['mobile_phone'], $test_sms_gateway_args['message']);

        return ['status' => 1];
    }

    public function clearQueue()
    {
        SmsEventQueueRepo::deletewhere(['user_id' => $this->user->id]);

        return ['status' => 1];
    }
}