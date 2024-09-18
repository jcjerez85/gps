<?php namespace ModalHelpers;

use App\Exceptions\DemoAccountException;
use App\Exceptions\PermissionException;
use CustomFacades\Repositories\DeviceGroupRepo;
use CustomFacades\Repositories\SmsEventQueueRepo;
use CustomFacades\Repositories\TimezoneRepo;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Validators\SMSGatewayFormValidator;
use CustomFacades\Validators\UserAccountFormValidator;
use CustomFacades\Validators\UserAccountSettingsFormValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\Client;
use Tobuli\Entities\Company;
use Tobuli\Entities\User;
use Tobuli\Entities\UserSecondaryCredentials;
use Tobuli\Services\UserClientService;
use Tobuli\Services\UserCompanyService;
use Tobuli\Services\UserService;


class MyAccountSettingsModalHelper extends ModalHelper
{
    private $data_group = [];

    public function editData()
    {
        $item = UserRepo::find($this->user->id)->toArray();
        $client = Client::find($item['client_id']) ?: new Client();
        $company = Company::find($item['company_id']);
        $timezones = TimezoneRepo::order()->pluck('title', 'id')->all();
        $groups = DeviceGroupRepo::getWhere(['user_id' => $this->user->id], 'title');

        $sms_queue_count = SmsEventQueueRepo::countwhere(['user_id' => $this->user->id]);
        $user_dst = DB::table('users_dst')->where('user_id', '=', $this->user->id)->first();

        if (! $item['timezone_id']) {
            $item['timezone_id'] = 17;
        }

        $sms_gateway_params = Arr::get($item, 'sms_gateway_params');
        $item['sms_gateway_params'] = array_merge([
            'request_method' => "",
            'encoding' => "",
            'authentication' => "",
            'custom_headers' => "",
            'username' => "",
        ], $sms_gateway_params ? $sms_gateway_params : []);
        $item['sms_gateway_url'] = $item['sms_gateway_url'] ?? "";

        $units_of_distance = [
            'km' => trans('front.kilometer'),
            'mi' => trans('front.mile'),
            'nm' => trans('front.nautical_mile')
        ];

        $units_of_capacity = [
            'lt' => trans('front.liter'),
            'gl' => trans('front.gallon')
        ];

        $units_of_altitude = [
            'mt' => trans('front.meter'),
            'ft' => trans('front.feet')
        ];

        $duration_formats = config('tobuli.duration_formats');

        $request_method_select = [
            'get' => 'GET',
            'post' => 'POST',
            'app' => trans('front.sms_gateway_app'),
            'plivo' => 'Plivo'
        ];

        if (settings('sms_gateway.enabled')) {
            $request_method_select = ['server' => 'Server gateway'] + $request_method_select;
        }

        $encoding_select = [0 => trans('global.no'), 'json' => 'JSON'];
        $authentication_select = [0 => trans('global.no'), 1 => trans('global.yes')];

        $dst_types = getDSTTypes();
        $months = getMonths();
        $weekdays = getWeekdays();
        $week_pos = getWeekPositions();
        $week_start_days = getWeekStartDays();
        $dst_countries = getDSTCountries();


        if ($this->api) {
            $timezones = apiArray($timezones);
            $units_of_distance = apiArray($units_of_distance);
            $units_of_capacity = apiArray($units_of_capacity);
            $units_of_altitude = apiArray($units_of_altitude);
            $request_method_select = apiArray($request_method_select);
            $encoding_select = apiArray($encoding_select);
            $authentication_select = apiArray($authentication_select);
            $dst_types = apiArray($dst_types);
            $months = apiArray($months);
            $dst_countries = apiArray($dst_countries);
            $week_start_days = apiArray($week_start_days);
            $week_pos = apiArray($week_pos);
            $weekdays = apiArray($weekdays);
        }

        if ($this->api) {
            return compact('item', 'timezones', 'units_of_distance',
                'units_of_capacity', 'units_of_altitude', 'groups',
                'sms_queue_count', 'request_method_select', 'encoding_select',
                'authentication_select', 'dst_types', 'user_dst',
                'months', 'weekdays', 'week_pos', 'dst_countries',
                'week_start_days', 'client', 'company');
        }

        if (empty($widgets = UserRepo::getSettings($this->user->id, 'widgets'))) {
            $widgets = settings('widgets');
        }

        $dashboard = getUserDashboardSettings($this->user);

        return compact('item', 'timezones', 'units_of_distance',
            'units_of_capacity', 'units_of_altitude', 'duration_formats', 'groups',
            'sms_queue_count', 'request_method_select', 'encoding_select',
            'authentication_select',
            'dst_types', 'user_dst', 'months', 'weekdays',
            'week_pos', 'dst_countries', 'week_start_days',
            'widgets', 'dashboard', 'client', 'company');
    }

    public function edit()
    {
        if (isDemoUser()) {
            throw new DemoAccountException();
        }

        $this->data['sms_gateway'] = (isset($this->data['sms_gateway']) && $this->data['sms_gateway']);
        $this->data['sms_gateway_url'] = isset($this->data['sms_gateway_url']) ? $this->data['sms_gateway_url'] : '';
        $item = $this->user;

        if ( ! empty($this->data['sms_gateway']) && isset($this->data['request_method'])) {
            SMSGatewayFormValidator::validate($this->data['request_method'], $this->data);
        }

        UserAccountSettingsFormValidator::validate('update', $this->data, $item->id);

        $update = Arr::only($this->data, [
            'sms_gateway',
            'sms_gateway_url',
            'unit_of_distance',
            'unit_of_capacity',
            'unit_of_altitude',
            'duration_format',
            'timezone_id',
            'week_start_day'
        ]);

        if (isset($this->data['request_method'])) {
            $fields = [
                'request_method',
                'authentication',
                'username',
                'password',
                'encoding',
                'auth_id',
                'auth_token',
                'senders_phone',
                'custom_headers'
            ];
            $update['sms_gateway_params'] = [];

            foreach ($fields as $field) {
                $value = '';

                if (isset($this->data[$field])) {
                    $value = $this->data[$field];
                } else {
                    if (isset($item->sms_gateway_params[$field])) {
                        $value = $item->sms_gateway_params[$field];
                    }
                }

                $update['sms_gateway_params'][$field] = $value;
            }
        }

        UserRepo::update($item->id, $update);

        if (isset($this->data['dst_type'])) {
            $userService = new UserService();
            $userService->setDST($this->user, array_merge($this->data, [
                'type' => $this->data['dst_type'] ?? null,
                'country_id' => $this->data['dst_country_id'] ?? null
            ]));
        }

        if ($this->user->can('edit', $this->user, 'client_id')) {
            $this->saveClient($item);

            if (!$item->company || $this->user->can('edit', $item->company)) {
                $this->saveCompany($item);
            }
        }

        # Object groups
        if ($this->api) {
            $arr = [];
            $groups = DeviceGroupRepo::getWhere(['user_id' => $item->id]);

            if (!$groups->isEmpty())
                $groups = $groups->pluck('id', 'id')->all();


            $this->data_group = [];

            if (isset($this->data['groups'])) {
                $this->data_group = $this->data['groups'];

                if ( ! is_array($this->data_group)) {
                    $this->data_group = json_decode($this->data_group, TRUE);
                }
            }

            foreach ($this->data_group as $key => $group) {
                $title = $group['title'];
                $id = $group['id'];

                if (empty($title)) {
                    continue;
                }

                if (array_key_exists($group['id'], $groups)) {
                    $arr[$id] = $id;
                    DeviceGroupRepo::updateWhere(['id' => $id, 'user_id' => $this->user->id], ['title' => $title]);
                } else {
                    $itemd = DeviceGroupRepo::create(['title' => $title, 'user_id' => $item->id]);
                    $id = $itemd->id;
                    $arr[$id] = $id;
                }
            }

            DeviceGroupRepo::deleteUsersWhereNotIn($arr, $item->id);
        }

        if ( ! $this->api) {
            $widgets = empty($this->data['widgets']) ? null : $this->data['widgets'];
            UserRepo::setSettings($item->id, 'widgets', $widgets);
            UserRepo::setSettings($item->id, 'dashboard', array_merge(settings('dashboard'), $this->data['dashboard']));
        }

        return ['status' => 1, 'id' => $item->id];
    }

    private function saveClient(User $user)
    {
        if (!isset($this->data['client'])) {
            return;
        }

        $input = $this->data['client'];

        if (!count(array_filter($input)) && !$user->client) {
            return;
        }

        (new UserClientService($user))->update($input);
    }

    private function saveCompany(User $user)
    {
        if (!isset($this->data['company'])) {
            return;
        }

        $input = $this->data['company'];

        if (!count(array_filter($input)) && !$user->company) {
            return;
        }

        if ($user->company && !$this->user->can('edit', $user->company)) {
            throw new PermissionException();
        }

        (new UserCompanyService($user))->update($input);
    }

    public function changePassword()
    {
        if (isDemoUser()) {
            throw new DemoAccountException();
        }

        $input = Arr::only($this->data, ['password']);

        UserAccountFormValidator::validate('password', $this->data);

        $item = $this->user->isMainLogin()
            ? User::find($this->user->id)
            : UserSecondaryCredentials::find($this->user->getLoginSecondaryCredentials()->id);

        $item->update($input);

        return ['status' => 1, 'id' => $item->id];
    }
}