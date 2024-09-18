<?php namespace Tobuli\Services;

use CustomFacades\Appearance;
use CustomFacades\Repositories\BillingPlanRepo;
use CustomFacades\Repositories\UserRepo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Tobuli\Entities\User;
use Tobuli\Helpers\Password;

class UserService
{
    /**
     * @var PermissionService
     */
    private $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    /**
     * @return array
     */
    public function getDefaults()
    {

        return [
            'active'           => true,
            'group_id'         => 2,
            'lang'             => Appearance::getSetting('default_language'),
            'unit_of_altitude' => Appearance::getSetting('default_unit_of_altitude'),
            'unit_of_distance' => Appearance::getSetting('default_unit_of_distance'),
            'unit_of_capacity' => Appearance::getSetting('default_unit_of_capacity'),
            'duration_format'  => Appearance::getSetting('default_duration_format'),
            'map_id'           => settings('main_settings.default_map'),
            'timezone_id'      => settings('main_settings.default_timezone'),
            'dst_date_from'    => settings('main_settings.dst') ? settings('main_settings.dst_date_from') : null,
            'dst_date_to'      => settings('main_settings.dst') ? settings('main_settings.dst_date_from') : null,

            'available_maps'   => settings('main_settings.available_maps'),
            'devices_limit' => null,
            'subscription_expiration' => '0000-00-00 00:00:00',
            'ungrouped_open' => ['geofence_group' => 1, 'device_group' => 1, 'poi_group' => 1],
        ];
    }

    /**
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        $data = array_merge($this->getDefaults(), $data);

        $user = UserRepo::create($data);
        $this->setDefaultTimezone($user);

        return $user;
    }

    /**
     * @param User $user
     */
    public function setDefaultTimezone(User $user)
    {
        if (! in_array(settings('main_settings.default_dst_type'), ['exact', 'other', 'automatic'])) {
            return;
        }

        $this->setDST($user, [
            'type' => settings('main_settings.default_dst_type'),
            'country_id' => settings('main_settings.default_dst_country_id'),
            'date_from' => settings('main_settings.default_dst_date_from'),
            'date_to' => settings('main_settings.default_dst_date_to'),
            'month_from' => settings('main_settings.default_dst_month_from'),
            'month_to' => settings('main_settings.default_dst_month_to'),
            'week_pos_from' => settings('main_settings.default_dst_week_pos_from'),
            'week_pos_to' => settings('main_settings.default_dst_week_pos_to'),
            'week_day_from' => settings('main_settings.default_dst_week_day_from'),
            'week_day_to' => settings('main_settings.default_dst_week_day_to'),
            'time_from' => settings('main_settings.default_dst_time_from'),
            'time_to' => settings('main_settings.default_dst_time_to'),
        ]);
    }

    public function setDST(User $user, array $data)
    {
        $values = [
            'type' => $data['type'],
            'country_id' => NULL,
            'date_from' => NULL,
            'date_to' => NULL,
            'month_from' => NULL,
            'month_to' => NULL,
            'week_pos_from' => NULL,
            'week_pos_to' => NULL,
            'week_day_from' => NULL,
            'week_day_to' => NULL,
            'time_from' => NULL,
            'time_to' => NULL
        ];

        switch ($data['type']) {
            case 'exact':
                $values = array_merge(
                    $values,
                    Arr::only($data,['date_from', 'date_to'])
                );
                break;
            case 'other':
                $values = array_merge(
                    $values,
                    Arr::only($data,['month_from', 'month_to', 'week_pos_from', 'week_pos_to', 'week_day_from', 'week_day_to', 'time_from', 'time_to'])
                );
                break;
            case 'automatic':
                $values = array_merge(
                    $values,
                    Arr::only($data,['country_id'])
                );
                break;
            default:
                DB::table('users_dst')->where('user_id', '=', $user->id)->delete();
                return;
        }

        DB::table('users_dst')->updateOrInsert(['user_id' => $user->id], $values);
    }

    /**
     * @param User $user
     * @param array $permissions
     */
    public function setPermissions(User $user, array $permissions)
    {
        DB::table('user_permissions')->where(['user_id' => $user->id])->delete();

        foreach ($permissions as $key => $val) {
            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'name'    => $key,
                'view'    => $val['view'],
                'edit'    => $val['edit'],
                'remove'  => $val['remove'],
            ]);
        }
    }

    /**
     * @param array $data
     * @return User
     */
    public function registration(array $data) {
        if (settings('main_settings.enable_plans') && settings('main_settings.default_billing_plan')) {
            $plan = BillingPlanRepo::find(settings('main_settings.default_billing_plan'));
            $data['devices_limit'] = $plan->objects;
            $data['billing_plan_id'] = settings('main_settings.default_billing_plan');

            if ($plan->price)
                $expiration = date('Y-m-d H:i:s');
            else
                $expiration = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')." + {$plan->duration_value} {$plan->duration_type}"));

            $data['subscription_expiration'] = $expiration;
        } else {
            $expiration_days = settings('main_settings.subscription_expiration_after_days');
            if (!is_null($expiration_days)) {
                $data['subscription_expiration'] = date('Y-m-d H:i:s', strtotime('+' . $expiration_days . ' days'));
            }
            $data['devices_limit'] = settings('main_settings.devices_limit');
        }

        $data['manager_id'] = NULL;
        if (Session::has('referer_id')) {
            $user = UserRepo::find(Session::get('referer_id'));
            if (!empty($user) && $user->isManager())
                $data['manager_id'] = $user->id;
        }

        $user = $this->create($data + ['group_id' => 2]);

        if (!(settings('main_settings.enable_plans') && settings('main_settings.default_billing_plan'))) {
            $permissions = $this->permissionService->getUserDefaults();
            $this->setPermissions($user, $permissions);
        }

        $this->setDefaultTimezone($user);

        return $user;
    }

    /**
     * @return string
     */
    public function generatePassword()
    {
        return Str::random(12);
    }

    public function setLoginToken(User $user)
    {
        do {
            $loginToken = Password::generate(64, ['uppercase', 'lowercase', 'numbers']);

            $validator = Validator::make(['token' => $loginToken], ['token' => Rule::unique('users', 'login_token')]);
        } while($validator->fails());

        $user->login_token = $loginToken;
        $user->save();
    }

    public function unsetLoginToken(User $user)
    {
        $user->login_token = null;
        $user->save();
    }
}