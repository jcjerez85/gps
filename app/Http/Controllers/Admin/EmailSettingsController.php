<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Repositories\Config\ConfigRepositoryInterface as Config;
use Tobuli\Validation\AdminEmailSettingsFormValidator;

class EmailSettingsController extends BaseController {
    /**
     * @var AdminEmailSettingsFormValidator
     */
    private $adminEmailSettingsFormValidator;
    /**
     * @var Config
     */
    private $config;

    function __construct(AdminEmailSettingsFormValidator $adminEmailSettingsFormValidator, Config $config) {
        parent::__construct();
        $this->adminEmailSettingsFormValidator = $adminEmailSettingsFormValidator;
        $this->config = $config;
    }

    public function index() {
        $settings = settings('email');
        if (isset($settings['signature'])) {
            $settings['signature'] = str_replace("<br>", "\r", $settings['signature']);
        }

        $providers = [
            'smtp' => trans('front.default'),
            'sendgrid' => 'SendGrid',
            'postmark' => 'Postmark',
            'mailgun' => 'Mailgun',
            'gpswoxmailer' => 'Mailer',
        ];

        return View::make('admin::EmailSettings.index')->with(compact('settings', 'providers'));
    }

    public function save() {
        $input = Request::all();
        $item_arr = settings('email');
        if (isset($item_arr['smtp_password']) && (!isset($input['smtp_password']) || empty($input['smtp_password'])))
            $input['smtp_password'] = $item_arr['smtp_password'];

        if (!isset($input['use_smtp_server']) || ($input['use_smtp_server'] != 0 && $input['use_smtp_server'] != 1))
            return Redirect::route('admin.email_settings.index')->withInput();

        try
        {
            if (config('app.server') == 'demo' && ! Auth::User()->isGod())
                throw new ValidationException(['id' => trans('front.demo_acc')]);
            
            if ($input['provider'] == 'smtp') {
                $this->adminEmailSettingsFormValidator->validate('use_smtp_server_' . $input['use_smtp_server'], $input);

                if ($input['use_smtp_server'] == 1) {
                    $update = [
                        'from_name' => $input['from_name'],
                        'noreply_email' => $input['noreply_email'],
                        'use_smtp_server' => $input['use_smtp_server'],
                        'smtp_server_host' => $input['smtp_server_host'],
                        'smtp_server_port' => $input['smtp_server_port'],
                        'smtp_security' => $input['smtp_security'],
                        'smtp_username' => $input['smtp_username'],
                        'smtp_password' => $input['smtp_password'],
                        'smtp_authentication' => $input['smtp_authentication'],
                    ];
                }
                else {
                    $update = [
                        'from_name' => $input['from_name'],
                        'noreply_email' => $input['noreply_email'],
                    ];
                }
            }
            elseif ($input['provider'] == 'mailgun') {
                $this->adminEmailSettingsFormValidator->validate('mailgun', $input);
                $update = [
                    'from_name' => $input['from_name'],
                    'noreply_email' => $input['noreply_email'],
                    'api_key' => $input['api_key'],
                    'domain' => $input['domain'],
                    'region' => $input['region'],
                ];
            }
            else {
                $this->adminEmailSettingsFormValidator->validate('sendgrid', $input);
                $update = [
                    'from_name' => $input['from_name'],
                    'noreply_email' => $input['noreply_email'],
                    'api_key' => $input['api_key'],
                ];
            }

            $update['provider'] = $input['provider'];
            $update['signature'] = str_replace(["\r\n", "\r", "\n"], "<br>", $input['signature']);

            settings('email', $update);

            Artisan::call('config:clear');

            return Redirect::route('admin.email_settings.index')->withSuccess(trans('front.successfully_saved'));
        }
        catch (ValidationException $e)
        {
            return Redirect::route('admin.email_settings.index')->withInput()->withErrors($e->getErrors());
        }
    }

    public function testEmail() {
        return View::make('admin::EmailSettings.test_email');
    }

    public function testEmailSend() {
        $input = Request::all();

        $res = \CustomFacades\MailHelper::send($input['email'], 'Test email', 'Test');

        if (!$res['status'])
            throw new ValidationException(['id' => $res['error']]);


        Session::flash('success', trans('front.successfully_saved'));

        return Response::json(['status' => 1, 'trigger' => 'window_reload']);
    }
}
