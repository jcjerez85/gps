<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tobuli\Helpers\SMS\SendSmsInterface;
use Tobuli\Helpers\SMS\Services\HTTP\SendSmsGET;
use Tobuli\Helpers\SMS\SMSGatewayManager;

class SMSGatewayManagerTest extends TestCase
{
    private $manager;

    public function setUp()
    {
        parent::setUp();

        Auth::loginUsingId(1, true);
        $this->manager = new SMSGatewayManager();
    }

    /** @test */
    public function get_gateway_args_return_array_of_arguments()
    {
        $args = $this->manager->getUserGatewayArgs();

        $this->assertInternalType('array', $args);
    }

    /** @test */
    public function load_sender_return_correct_instance_object()
    {
        $object = $this->manager->loadSender();

        $this->assertInstanceOf(SendSmsInterface::class, $object);
    }

//    /** @test */
//    public function test_get_sms_gateway_throws_exception_with_bad_url()
//    {
//        $this->setExpectedException(\Tobuli\Exceptions\ValidationException::class);
//
//        $bad_url = 'BAD:URL';
//
//        $args = $this->manager->getUserGatewayArgs();
//        $args['sms_gateway_url'] = $bad_url;
//
//        $get = new \Tobuli\Helpers\SMS\Services\SendSmsGET($args);
//        $get->send('654654654', 'body');
//    }

    /** @test */
    public function testGetMethod()
    {
        $args = $this->manager->getUserGatewayArgs();
        $get = new SendSmsGET($args);

        $res = $get->send('654654654', 'body');

        var_dump($res);
    }

}
