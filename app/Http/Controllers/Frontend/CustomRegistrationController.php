<?php namespace App\Http\Controllers\Frontend;

use App\Exceptions\PaymentsIssueException;
use App\Exceptions\PaymentsUnavailableException;
use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Curl;
use CustomFacades\ModalHelpers\RegistrationModalHelper;
use CustomFacades\Validators\RegistrationFormValidator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\Device;
use Tobuli\Entities\DevicePlan;
use Tobuli\Entities\DeviceType;
use Tobuli\Entities\DeviceTypeImei;
use Tobuli\Entities\Order;
use Tobuli\Entities\Subscription;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\Payments\Payments;
use Tobuli\Services\CustomValuesService;
use Tobuli\Services\DeviceSensorsService;
use Tobuli\Services\DeviceService;
use Tobuli\Services\UserService;

class CustomRegistrationController extends Controller
{
    private $customValuesService;
    private $deviceService;

    public function __construct(CustomValuesService $customValuesService, DeviceService $deviceService) {
        parent::__construct();

        //if (!settings('main_settings.allow_users_registration'))
        //    abort(404);

        $this->customValuesService = $customValuesService;
        $this->deviceService = $deviceService;

        $this->tabs = [
            'user'     => trans('front.user_info'),
            'device'   => trans('global.device'),
            'plan'     => trans('admin.device_plan'),
            'review'   => trans('front.review'),
            'checkout' => trans('front.checkout'),
        ];

        $this->tabsDeviceAdd = ['device', 'plan', 'review', 'checkout'];
    }

    public function afterAuth($user)
    {
        if (empty($user->phone_number)) {
            array_unshift($this->tabsDeviceAdd, 'user');
        }
    }

    public function create()
    {
        if ($this->user) {
            return Redirect::route( 'register.step.create', 'user');
        }

        return view('front::CustomRegistration.create');
    }

    public function store()
    {
        if ($this->user) {
            return Redirect::route( 'register.step.create', 'user');
        }

        $userService = new UserService();

        $validator = Validator::make(request()->all(), [
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|strong_password|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('register.create')
                ->withInput()
                ->withErrors($validator->errors());
        }

        $user = $userService->registration([
            'email'    => $this->data['email'],
            'password' => $this->data['password']
        ]);

        Auth::loginUsingId($user->id);

        return redirect()->route('register.index');
    }

    public function index()
    {
        reset($this->tabs);

        return $this->stepCreate(key($this->tabs));
    }

    public function stepCreate($step)
    {
        if (!array_key_exists($step, $this->tabs)) {
            abort(404);
        }

        if ($step !== 'user')
            $result = $this->accIncomplete();

        if (empty($result))
            $result = $this->{"{$step}Step"}();

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return view('front::CustomRegistration.index', $result);
    }

    public function stepStore($step)
    {
        if (!array_key_exists($step, $this->tabs)) {
            abort(404);
        }

        return $this->{"{$step}StepStore"}();
    }

    private function userStep()
    {
        return [
            'tabs' => Arr::only($this->tabs, $this->tabsDeviceAdd),
            'step' => 'user',
            'item' => $this->user
        ];
    }

    private function userStepStore()
    {
        $validator = Validator::make(request()->all(), [
            'phone_number' => 'required|phone',
        ]);

        if ($validator && $validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $this->user->update([
            'phone_number' => request()->get('phone_number')
        ]);

        $customValues = $this->data['custom_fields'] ?? null;
        $this->customValuesService->saveCustomValues($this->user, $customValues);

        return [
            'next' => route('register.step.create', 'device')
        ];
    }

    private function deviceStep()
    {
        $device_id = $this->getDeviceId();

        $device = $device_id ? Device::find($device_id) : new Device();

        if ($device_id) {
            $this->checkException('devices', 'own', $device);
        }

        return [
            'tabs' => Arr::only($this->tabs, $this->tabsDeviceAdd),
            'step' => 'device',
            'item' => $device,
            'deviceTypes' => DeviceType::active()->get()
        ];
    }

    private function deviceStepStore()
    {
        $device_id = request()->id;
        $device = Device::find($device_id);

        $deviceTypeImei = DeviceTypeImei::where('imei', request()->get('imei'))
            ->first();

        $rules = [
            'imei' => 'required|unique:devices,imei' . ( $device ? ",{$device->id}" : "") . "|exists:device_type_imeis,imei",
            'name' => 'required',
        ];

        $validator = Validator::make(request()->all(), $rules);

        if ($validator && $validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        $data = array_replace($this->data, [
            'user_id' => [$this->user->id],
            'expiration_date' => Carbon::now()
                ->subDay()
                ->format('Y-m-d H:i:s'),
            'device_type_id' => $deviceTypeImei->device_type_id ?? null,
            'msisdn' => $deviceTypeImei ? $deviceTypeImei->msisdn : ""
        ]);

        if ($device) {
            $this->checkException('devices', 'own', $device);

            $device = $this->deviceService->update($device, $data);
        } else {
            $device = $this->deviceService->create($data);

            $deviceType = DeviceType::find($device->device_type_id);
            if ($deviceType->sensor_group_id) {
                $sensorsService = new DeviceSensorsService();
                $sensorsService->addSensorGroup($device, $this->user, $deviceType->sensor_group_id);
            }

            session(['activation_device_id' => $device->id]);
        }

        $customValues = $this->data['custom_fields'] ?? null;
        $this->customValuesService->saveCustomValues($device, $customValues);

        return [
            'next' => route('register.step.create', [
                'step' => 'plan',
                'device_id' => $device->id
            ])
        ];
    }

    private function planStep()
    {
        $device = Device::find($this->getDeviceId());

        if (empty($device)) {
            return Redirect::route( 'register.step.create', 'device');
        }

        $this->checkException('devices', 'own', $device);

        $plans = DevicePlan::whereIn('duration_type', ['months', 'years'])
            ->where('duration_value', 1)
            ->where('active', 1)
            ->forDevice($device)
            ->orderBy('price')
            ->get();

        session()->forget('activation_device_id');
        session()->put(['activation_device_id' => $device->id]);

        return [
            'backUrl' => route('register.step.create', 'device'),
            'tabs'  => Arr::only($this->tabs, $this->tabsDeviceAdd),
            'step'  => 'plan',
            'plans' => $plans,
            'device_plan_id' => session()->get('activation_device_plan_id', $plans->first()->id ?? null),
        ];
    }

    private function planStepStore()
    {
        $validator = Validator::make(request()->all(), [
            'device_plan_id' => 'required',
        ]);

        if ($validator && $validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        session(['activation_device_plan_id' => request()->get('device_plan_id')]);

        return [
            'next' => route('register.step.create', [
                'step' => 'review',
            ])
        ];
    }

    private function reviewStep()
    {
        $device = Device::with('deviceType')->find($this->getDeviceId());

        if (empty($device)) {
            return Redirect::route( 'register.step.create', 'device');
        }

        $this->checkException('devices', 'own', $device);

        return [
            'backUrl' => route('register.step.create', 'plan'),
            'tabs'  => Arr::only($this->tabs, $this->tabsDeviceAdd),
            'step'  => 'review',
            'items' => [
                [
                    'device' => $device,
                    'plan'   => DevicePlan::findOrFail(session()->get('activation_device_plan_id')),
                ]
            ]
        ];
    }

    private function reviewStepStore()
    {
        $device = Device::find($this->getDeviceId());
        $this->checkException('devices', 'own', $device);

        $plan = DevicePlan::findOrFail(session()->get('activation_device_plan_id'));

        $order = Order::create([
            'user_id'     => $this->user->id,
            'plan_id'     => $plan->id,
            'plan_type'   => 'device_plan',
            'price'       => $plan->price,
            'entity_id'   => $device->id,
            'entity_type' => 'device',
        ]);

        session()->forget('activation_device_id');
        session()->forget('activation_device_plan_id');

        return [
            'next' => route('register.step.create', [
                'step' => 'checkout',
                'order_id' => $order->id
            ]),
        ];
    }

    private function checkoutStep()
    {
        $order = Order::find(request()->order_id);

        if (empty($order)) {
            return Redirect::route( 'register.step.create', 'device');
        }

        $this->checkException('orders', 'own', $order);

        if ($order->isPaid()) {
            session()->flash('message', trans('front.order_already_paid'));
        }

        $paymentIntent = (new Payments('stripe'))->getPaymentIntent($order);

        return [
            'tabs'   => Arr::only($this->tabs, $this->tabsDeviceAdd),
            'step'   => 'checkout',

            'order'  => $order,

            'payment_intent' => $paymentIntent,
        ];
    }

    public function checkoutStepStore()
    {
        $request = request();
        $order = Order::find($request->order_id);

        if (empty($order)) {
            return Redirect::back()
                ->withInput()
                ->with('message', trans('front.order_not_found'));
        }

        if ($order->isPaid()) {
            return Redirect::back()
                ->withInput()
                ->with('message', trans('front.order_already_paid'));
        }

        try {
            $paymentProvider = new Payments('stripe');

            $paymentProvider->subscribe($this->user, $order);

            $subscription = Subscription::where('order_id', $order->id)->first();
            $request->merge([
                'subscription_id' => $subscription->gateway_id,
                'intent_id' => $request['intent'],
            ]);

            $paymentProvider->subscribeCallback($request);

            return Redirect::route( 'register.success' )->with( 'order', $order );

        } catch (PaymentsIssueException $exception) {
            return Redirect::back()
                ->withInput()
                ->with('message', $exception->getMessage());
        } catch (PaymentsUnavailableException $exception) {
            return Redirect::back()
                ->withInput()
                ->with('message', $exception->getMessage());
        }
    }

    public function success()
    {
        $order = session()->get('order');

        if (!$order) {
            return Redirect::route('register.step.create', [
                'step' => 'device',
            ]);
        }

        return view('front::CustomRegistration.success', [
            'order' => $order,
        ]);
    }

    private function getDeviceId()
    {
        $device_id = request()->get('device_id', session()->get('activation_device_id'));

        return $device_id;
    }

    private function accIncomplete()
    {
        if ( ! empty($this->user->phone_number))
            return false;

        return Redirect::route( 'register.step.create', 'user');
    }
}