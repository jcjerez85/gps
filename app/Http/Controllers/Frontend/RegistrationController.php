<?php namespace App\Http\Controllers\Frontend;

use Curl;
use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\RegistrationModalHelper;
use Tobuli\Exceptions\ValidationException;

class RegistrationController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!settings('main_settings.allow_users_registration')) {
            abort(404);
        }
    }

    public function create()
    {
        if (config('addon.custom_device_add')) {
            return view('front::CustomRegistration.create');
        }

        return view('front::Registration.create');
    }

    public function store()
    {
        $data = RegistrationModalHelper::create();

        if ($this->api) {
            return $data;
        }

        if ($data['status']) {
            return redirect()->route('login')
                ->with('success', trans('front.registration_successful'));
        }

        return redirect()->route('registration.create')
            ->withInput()
            ->withErrors($data['errors']);
    }

    public function createCustom()
    {
        return view('front::Registration.create');
    }

    public function storeCustom()
    {
        $data = RegistrationModalHelper::create();

        if ($this->api) {
            return $data;
        }

        if ($data['status']) {
            return redirect()->route('login')
                ->with('success', trans('front.registration_successful'));
        }

        return redirect()->route('registration.create')
            ->withInput()
            ->withErrors($data['errors']);
    }
}
