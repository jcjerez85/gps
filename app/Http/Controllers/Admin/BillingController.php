<?php namespace App\Http\Controllers\Admin;


use Collective\Html\FormFacade as Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config as LaravelConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\Payments\Payments;
use Tobuli\Repositories\BillingPlan\BillingPlanRepositoryInterface as BillingPlan;
use Tobuli\Repositories\Timezone\TimezoneRepositoryInterface as Timezone;
use Tobuli\Services\PermissionService;
use Tobuli\Validation\AdminBillingPlanFormValidator;


class BillingController extends BaseController
{
    private $payment_types = [
        'paypal' => 'Paypal',
        'stripe' => 'Stripe'
    ];

    private $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        parent::__construct();

        $this->permissionService = $permissionService;
    }

    public function index(BillingPlan $billingPlanRepo, Timezone $timezoneRepo) {
        $items = $billingPlanRepo->getWhere([], 'objects', 'asc');

        $settings = settings('main_settings');

        $timezones = $timezoneRepo->order()->pluck('title', 'id')->all();
        $payment_types = $this->payment_types;
        $grouped_permissions = $this->permissionService->group(
            $this->permissionService->getByManagerRole()
        );

        $dst_types = getDSTTypes();
        $months = getMonths();
        $weekdays = getWeekdays();
        $week_pos = getWeekPositions();
        $week_start_days = getWeekStartDays();
        $dst_countries = getDSTCountries();

        return view('admin::Billing.' . (Request::ajax() ? 'table' : 'index'))
            ->with(compact('items', 'timezones', 'settings',
                'payment_types', 'grouped_permissions', 'dst_types', 'months',
                'weekdays', 'week_pos', 'week_start_days', 'dst_countries'));
    }

    public function create() {
        $duration_types = [
            'days' => trans('front.days'),
            'months' => trans('front.months'),
            'years' => trans('front.years'),
        ];

        $grouped_permissions = $this->permissionService->group(
            $this->permissionService->getByManagerRole()
        );
        $permission_values = $this->permissionService->getByManagerRole();

        return view('admin::Billing.create')->with(compact('duration_types', 'grouped_permissions', 'permission_values'));
    }

    public function planStore(BillingPlan $billingPlanRepo, AdminBillingPlanFormValidator $adminBillingPlanFormValidator) {
        $input = Request::all();
        $permissions = LaravelConfig::get('tobuli.permissions');

        $adminBillingPlanFormValidator->validate('create', $input);

        beginTransaction();
        try {

            $plan = $billingPlanRepo->create($input);

            if (array_key_exists('perms', $input)) {
                foreach ($permissions as $key => $val) {
                    if ( ! array_key_exists($key, $input['perms']))
                        continue;

                    DB::table('billing_plan_permissions')->insert([
                        'plan_id' => $plan->id,
                        'name' => $key,
                        'view' => $val['view'] && (Arr::get($input['perms'][$key], 'view') || Arr::get($input['perms'][$key], 'edit') || Arr::get($input['perms'][$key], 'remove')) ? 1 : 0,
                        'edit' => $val['edit'] && Arr::get($input['perms'][$key], 'edit') ? 1 : 0,
                        'remove' => $val['remove'] && Arr::get($input['perms'][$key], 'remove') ? 1 : 0
                    ]);
                }
            }

        } catch (\Exception $e) {
            rollbackTransaction();
            throw new ValidationException(['id' => trans('global.unexpected_db_error')]);
        }
        commitTransaction();

        return response()->json(['status' => 1]);
    }

    public function edit($id, BillingPlan $billingPlanRepo) {
        $item = $billingPlanRepo->find($id);
        if (empty($item))
            return modalError(dontExist('validation.attributes.plan'));

        $duration_types = [
            'days' => trans('front.days'),
            'months' => trans('front.months'),
            'years' => trans('front.years'),
        ];

        $grouped_permissions = $this->permissionService->group(
            $this->permissionService->getByManagerRole()
        );
        $permission_values = $item->getPermissions();

        return view('admin::Billing.edit')->with(compact('item', 'duration_types', 'grouped_permissions', 'permission_values'));
    }

    public function update(BillingPlan $billingPlanRepo, AdminBillingPlanFormValidator $adminBillingPlanFormValidator) {
        $input = Request::all();
        $permissions = LaravelConfig::get('tobuli.permissions');

        $adminBillingPlanFormValidator->validate('create', $input);

        beginTransaction();
        try {
            $billingPlanRepo->update($input['id'], $input);

            DB::table('billing_plan_permissions')->where('plan_id', '=', $input['id'])->delete();
            if (array_key_exists('perms', $input)) {
                foreach ($permissions as $key => $val) {
                    if ( ! array_key_exists($key, $input['perms']))
                        continue;

                    DB::table('billing_plan_permissions')->insert([
                        'plan_id' => $input['id'],
                        'name' => $key,
                        'view' => $val['view'] && (Arr::get($input['perms'][$key], 'view') || Arr::get($input['perms'][$key], 'edit') || Arr::get($input['perms'][$key], 'remove')) ? 1 : 0,
                        'edit' => $val['edit'] && Arr::get($input['perms'][$key], 'edit') ? 1 : 0,
                        'remove' => $val['remove'] && Arr::get($input['perms'][$key], 'remove') ? 1 : 0
                    ]);
                }
            }

        } catch (\Exception $e) {
            rollbackTransaction();
            throw new ValidationException(['id' => trans('global.unexpected_db_error')]);
        }
        commitTransaction();


        return response()->json(['status' => 1]);
    }

    public function plans(BillingPlan $billingPlanRepo) {
        $items = $billingPlanRepo->getWhere([], 'objects', 'asc');

        return view('admin::Billing.table')->with(compact('items'));
    }

    public function billingPlansForm(BillingPlan $billingPlanRepo) {
        $items = $billingPlanRepo->all()->pluck('title', 'id')->all();

        return Form::select('default_billing_plan', $items, settings('main_settings.default_billing_plan'), ['class' => 'form-control']);
    }

    public function destroy(BillingPlan $billingPlanRepo) {
        $input = Request::all();
        if (!isset($input['id']))
            return response()->json(['status' => 0]);

        $ids = $input['id'];

        $settings = settings('main_settings');
        if (settings('main_settings.enable_plans')) {
            foreach ($ids as $key => $val) {
                if ($settings['default_billing_plan'] == $val) {
                    unset($ids[$key]);
                    break;
                }
            }
        }

        $billingPlanRepo->deleteWhereIn($ids);

        return response()->json(['status' => 1]);
    }

    public function gateways()
    {
        $visible = [];
        foreach (config('payments') as $payment => $config) {
            if ( ! $config['visible'])
                continue;

            $visible[] = $payment;
        }

        $plans = [];
        foreach (\Tobuli\Entities\BillingPlan::all() as $plan) {
            $plans[$plan->id] = [
                'title'        => $plan->title,
                'braintree_id' => settings('payments.braintree.plans.' . $plan->id),
            ];
        }

        try {
            $braintree_plan_ids = (new Payments('braintree'))->getPlanIds();
        } catch (\Exception $e) {
            $braintree_plan_ids = [];
        }

        return view('admin::Billing.gateways')->with([
            'gateways'           => Arr::only(settings('payments.gateways'), $visible),
            'plans'              => $plans,
            'braintree_plan_ids' => $braintree_plan_ids,
        ]);
    }

    public function gatewayConfigStore(\Illuminate\Http\Request $request, $gateway)
    {
        try {
            $validator = 'CustomFacades\Validators\\' . ucfirst(Str::camel($gateway)) . 'ConfigFormValidator';
            $validator::validate('update', $request->except('_token', 'active'));

            (new Payments($gateway))->storeConfig($request, $gateway);
        } catch (ValidationException $e) {
            return Redirect::route('admin.billing.gateways')->withInput()->withBillingErrors($e->getErrors());
        } catch (\Exception $e) {
            return Redirect::route('admin.billing.gateways')->withInput()->withBillingErrors(['id' => $e->getMessage()]);
        }

        return Redirect::back()->withBillingSuccess(trans('front.successfully_saved'));
    }
}
