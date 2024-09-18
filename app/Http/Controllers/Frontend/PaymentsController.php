<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\PaymentsConfigurationException;
use App\Exceptions\PaymentsIssueException;
use App\Exceptions\PaymentsUnavailableException;
use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Tobuli\Entities\BillingPlan;
use Tobuli\Helpers\Payments\Payments;
use Illuminate\Support\Facades\Config;
use Tobuli\Entities\Device;
use Tobuli\Entities\DevicePlan;
use Tobuli\Entities\Order;
use Tobuli\Entities\Subscription;
use Tobuli\Repositories\BillingPlan\BillingPlanRepositoryInterface as BillingPlanRepo;
use Tobuli\Services\PermissionService;


class PaymentsController extends Controller
{
    private $permissionService;

    /**
     * PaymentsController constructor.
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        parent::__construct();

        $this->permissionService = $permissionService;
    }

    /**
     * @param $gateway
     * @param $plan_id
     * @return mixed
     */
    public function pay($gateway, $order_id)
    {
        $order = Order::find($order_id);

        if (empty($order))
            return Redirect::back()->with(['message' => trans('front.plan_not_found')]);

        try {
            return (new Payments($gateway))->pay($this->user, $order);
        } catch (\Exception $exception) {
            return Redirect::route('payments.subscriptions')->with(['message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $gateway
     * @return mixed
     */
    public function payCallback(Request $request, $gateway)
    {
        try {
            return (new Payments($gateway))->payCallback($request);
        } catch (\Exception $exception) {
            return Redirect::route('payments.subscriptions')->with(['message' => $exception->getMessage()]);
        }
    }

    /**
     * @param $gateway
     * @param $order_id
     * @return mixed
     */
    public function subscribe($gateway, $order_id)
    {
        $order = Order::find($order_id);

        if (empty($order))
            return Redirect::back()->with(['message' => trans('front.plan_not_found')]);

        try {
            return (new Payments($gateway))->subscribe($this->user, $order);
        } catch (PaymentsIssueException $exception) {
            return Redirect::route('payments.gateways', [
                    'order_id' => $order->id,
                ])
                ->with(['message' => $exception->getMessage()]);
        } catch (PaymentsUnavailableException $exception) {
            return Redirect::route('payments.gateways', [
                    'order_id' => $order->id,
                ])
                ->with(['message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $gateway
     * @return mixed
     */
    public function subscribeCallback(Request $request, $gateway)
    {
        try {
            return (new Payments($gateway))->subscribeCallback($request);
        } catch (PaymentsIssueException $exception) {
            return Redirect::route('payments.subscriptions')->with(['message' => $exception->getMessage()]);
        } catch (PaymentsUnavailableException $exception) {
            return Redirect::route('payments.subscriptions')->with(['message' => $exception->getMessage()]);
        }
    }

    /**
     * Success route after payment.
     *
     * @return mixed
     */
    public function success()
    {
        return view('front::Subscriptions.success')->with([
            'message' => trans('front.payment_received')
        ]);
    }

    /**
     * Cancel route if anything goes wrong.
     *
     * @return mixed
     */
    public function cancel()
    {
        return Redirect::route('payments.subscriptions')->with('message', trans('front.payment_canceled'));
    }

    /**
     * Select subscription plan view.
     *
     * @param BillingPlanRepo $billingPlanRepo
     */
    public function subscriptions(BillingPlanRepo $billingPlanRepo)
    {
        if (config('addon.custom_device_add'))
            return Redirect::route('subscriptions.renew');

        if ( ! settings('main_settings.enable_plans'))
            return Redirect::route('home');

        $permissions = $this->permissionService->group(
            $this->permissionService->getByManagerRole()
        );

        $plans = $billingPlanRepo->getWhere(['visible' => true], 'objects', 'asc');

        return view('front::Subscriptions.renew')->with(compact('plans', 'permissions'));
    }

    public function order($type, $plan_id, $entity_type)
    {
        if ($entity_type == 'user' && config('tobuli.type') == 'public') {
            $url = config('tobuli.frontend_subscriptions') . "?email=" . base64_encode(auth()->user()->email);

            return Redirect::to($url);
        }

        if (! in_array($type, array_keys(Order::getPlanTypes()))) {
            throw new ResourseNotFoundException('front.plan_not_found');
        }

        if (is_null(Order::getEntityType($entity_type))) {
            throw new ResourseNotFoundException('front.plan_not_found');
        }

        $entity_id = $type == 'device_plan' ? ($this->data['entity'] ?? null) : $this->user->id;

        if ($entity_id && $entity_type == 'device') {
            $device = Device::find($entity_id);
            $this->checkException('devices', 'show', $device);

            $plan = DevicePlan::active()->forDevice($device)->find($plan_id);

            if (empty($plan)) {
                throw new ResourseNotFoundException('front.plan_not_found');
            }
        }

        $model = Order::getPlanByType($type);

        if (is_null($model)) {
            throw new ResourseNotFoundException('front.plan_not_found');
        }

        $plan = $model::find($plan_id);

        if (empty($plan)) {
            throw new ResourseNotFoundException('front.plan_not_found');
        }

        $order = Order::create([
            'user_id'     => $this->user->id,
            'plan_id'     => $plan_id,
            'plan_type'   => $type,
            'price'       => $plan->price,
            'entity_id'   => $entity_id,
            'entity_type' => $entity_type,
        ]);

        //@TODO: can device plan be free?
        if ($entity_type == 'user' && $plan->isFree()) {
            return Redirect::route('payments.pay', ['gateway' => 'free', 'order_id' => $order->id]);
        }

        return Redirect::route('payments.gateways', ['order_id' => $order->id]);
    }

    /**
     * Select gateway view.
     *
     * @param $plan_id
     * @return mixed
     */
    public function selectGateway($order_id)
    {
        $order = Order::find($order_id);

        $this->checkException('orders', 'view', $order);

        $visible  = [];
        $gateways = settings('payments.gateways');

        foreach (config('payments') as $gateway => $config) {
            if (( ! $config['visible']) || ( ! $gateways[$gateway]))
                continue;

            $visible[] = $gateway;
        }

        return view('front::Subscriptions.gateways', [
            'gateways' => $visible,
            'order_id'  => $order->id,
        ]);
    }

    /**
     * Redirects to payment method.
     *
     * @param Request $request
     * @return mixed
     */
    public function checkout(Request $request)
    {
        if ( ! settings('payments.gateways.' . $request->gateway)) {
            return Redirect::back();
        }

        $order = Order::find($request->order_id);

        $this->checkException('orders', 'view', $order);

        try {
            return (new Payments($request->gateway))->checkout($order);
        } catch (PaymentsUnavailableException $exception) {
            return Redirect::route('payments.subscriptions')
                ->with(['message' => $exception->getMessage()]);
        } catch (\Exception $exception) {
            return Redirect::route('payments.subscriptions')
                ->with(['message' => trans('front.payments_service_unavailable')]);
        }
    }

    /**
     * Webhook for gateway to send data
     * @param string $gateway
     * @return Response
     */
    public function webhook(Request $request, $gateway)
    {
        try {
            return (new Payments($gateway))->webhook($request);
        } catch (PaymentsIssueException $exception) {
            return response($exception->getMessage(), 422);
        }
    }

    /**
     * Checks gateway configuration.
     *
     * @param Request $request
     * @param $gateway
     * @return \Illuminate\Http\JsonResponse
     */
    public function isConfigCorrect(Request $request, $gateway)
    {
        try {
            (new Payments($gateway))->isConfigCorrect($request);
        } catch (PaymentsConfigurationException $exception) {
            return response()->json(['status' => 0, 'error' => $exception->getMessage()]);
        }

        return response()->json(['status' => 1]);
    }

    /**
     * Modal for gateways specific information.
     *
     * @param $gateway
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function gatewayInfo($gateway)
    {
        return view('Admin.Billing.Gateways.Info.' . $gateway);
    }
}
