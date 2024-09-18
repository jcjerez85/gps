<?php namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\SmsGatewayModalHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SmsGatewayController extends Controller
{

    public function testSms()
    {
        return view('front::SmsGateway.test_sms');
    }

    public function sendTestSms()
    {
        return SmsGatewayModalHelper::sendTestSms();
    }

    public function clearQueue()
    {
        return SmsGatewayModalHelper::clearQueue();
    }
}