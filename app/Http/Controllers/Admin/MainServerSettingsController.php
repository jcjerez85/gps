<?php namespace App\Http\Controllers\Admin;

use CustomFacades\Appearance;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config as LaravelConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\LbsLocation\LbsManager;
use Tobuli\Repositories\Config\ConfigRepositoryInterface as Config;
use Tobuli\Repositories\Timezone\TimezoneRepositoryInterface as Timezone;
use Tobuli\Validation\AdminLogoUploadValidator;
use Tobuli\Validation\AdminMainServerSettingsFormValidator;
use Tobuli\Validation\AdminNewUserDefaultsFormValidator;

class MainServerSettingsController extends BaseController {
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Timezone
     */
    private $timezone;
    /**
     * @var AdminMainServerSettingsFormValidator
     */
    private $adminMainServerSettingsFormValidator;
    /**
     * @var AdminNewUserDefaultsFormValidator
     */
    private $adminNewUserDefaultsFormValidator;

    function __construct(AdminMainServerSettingsFormValidator $adminMainServerSettingsFormValidator, Config $config, Timezone $timezone, AdminNewUserDefaultsFormValidator $adminNewUserDefaultsFormValidator) {
        parent::__construct();
        $this->config = $config;
        $this->timezone = $timezone;
        $this->adminMainServerSettingsFormValidator = $adminMainServerSettingsFormValidator;
        $this->adminNewUserDefaultsFormValidator = $adminNewUserDefaultsFormValidator;
    }

    public function index() {
        if (!($this->user->isAdmin() || $this->user->isReseller()))
            return redirect(route('objects.index'));

        if ($this->user->isAdmin()) {
            $settings = settings('main_settings');
        } else {
            Appearance::setUser($this->user);
            $settings = Appearance::getSettings();
        }

        $maps = getMaps();

        $langs = Arr::sort(settings('languages'), function($language){
            return $language['title'];
        });

        $timezones = $this->timezone->order()->pluck('title', 'id')->all();
        $units_of_distance = LaravelConfig::get('tobuli.units_of_distance');
        $units_of_capacity = LaravelConfig::get('tobuli.units_of_capacity');
        $units_of_altitude = LaravelConfig::get('tobuli.units_of_altitude');
        $date_formats = LaravelConfig::get('tobuli.date_formats');
        $time_formats = LaravelConfig::get('tobuli.time_formats');
        $duration_formats = LaravelConfig::get('tobuli.duration_formats');
        $object_online_timeouts = LaravelConfig::get('tobuli.object_online_timeouts');
        $zoom_levels = LaravelConfig::get('tobuli.zoom_levels');

        $geocoder_apis = [
            'default' => trans('front.default'),
            'google' => 'Google API',
            'openstreet' => 'OpenStreet API',
            'geocodio' => 'Geocod.io API',
            'locationiq' => 'LocationIQ API',
            'nominatim' => 'Nominatim',
            'here' => "HERE API",
            'longdo' => 'Longdo API',
            'mapmyindia' => "MapMyIndia API",
            'pickpoint' => "PickPoint API",
            'positionstack' => "PositionStack API",
        ];

        // Is geocoder cache enabled
        $geocoder_cache_status = [
            1 => 'Enabled',
            0 => 'Disabled'
        ];

        $streetview_api = settings('main_settings.streetview_api');
        $streetview_key = settings('main_settings.streetview_key');
        $streetview_apis = [
            'google'    => 'Google Streetview API',
            'mapillary' => 'Mapillary'
        ];

        if (config('services.streetview.default'))
            $streetview_apis['default'] = trans('front.default');

        // How long to keep geocoder cache
        $days_range = range(5, 360, 5);
        $geocoder_cache_days = array_combine($days_range, $days_range);

        $captcha_providers = [
            'none' => trans('front.none'),
            'default' => trans('validation.attributes.default'),
            'recaptcha' => trans('validation.attributes.google_recaptcha'),
        ];

        $lbs_providers = ['' => trans('front.none')] + LbsManager::PROVIDERS;

        $extra_expiration_time_options = [
            0 => trans('front.none'),
            3600 => '1 ' . ($h = trans('front.hour_short')),
            3600 * 2 => "2 $h",
            3600 * 3 => "3 $h",
            3600 * 6 => "6 $h",
            3600 * 12 => "12 $h",
            3600 * 24 => "24 $h",
            3600 * 48 => "48 $h",
        ];

        return View::make('admin::MainServerSettings.index')
            ->with(compact('settings', 'maps', 'langs', 'timezones', 'units_of_distance',
                'units_of_capacity', 'units_of_altitude', 'date_formats', 'time_formats', 'duration_formats',
                'object_online_timeouts', 'geocoder_apis', 'zoom_levels', 'geocoder_cache_status',
                'geocoder_cache_days', 'streetview_apis', 'streetview_api', 'streetview_key',
                'captcha_providers', 'lbs_providers', 'extra_expiration_time_options'));
    }

    public function save() {
        if (!($this->user->isAdmin() || $this->user->isReseller()))
            return redirect(route('objects.index'));

        $input = request()->except('_token');

        try
        {
            $this->adminMainServerSettingsFormValidator->validate('update', $input);

            beginTransaction();
            try {
                if ($this->user->isAdmin()) {
                    $settings = array_merge(settings('main_settings'), $input);
                    settings('main_settings', $settings);

                    DB::table('users')
                        ->whereNotIn('map_id', $input['available_maps'])
                        ->update([
                            'map_id' => $input['default_map']
                        ]);
                } else {
                    Appearance::setUser($this->user);
                    Appearance::save($input);
                }
            }
            catch (\Exception $e) {
                rollbackTransaction();
                throw new ValidationException(['id' => trans('global.unexpected_db_error')]);
            }

            commitTransaction();

            return Redirect::route('admin.main_server_settings.index')->withSuccess(trans('front.successfully_saved'));
        }
        catch (ValidationException $e)
        {
            return Redirect::route('admin.main_server_settings.index')->withInput()->withErrors($e->getErrors());
        }
    }

    public function logoSave(AdminLogoUploadValidator $adminLogoUploadValidator)
    {
        if (!($this->user->isAdmin() || $this->user->isReseller()))
            return redirect(route('objects.index'));

        $requestData = request()->all();

        try {
            $adminLogoUploadValidator->validate('update', $requestData);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.main_server_settings.index')
                ->withErrors($e->getErrors());
        }

        Appearance::setUser($this->user);
        $success = Appearance::save($requestData);

        if (!$success) {
            return redirect()
                ->route('admin.main_server_settings.index')
                ->withErrors(trans('validation.invalid_value'));
        }

        return redirect()->route('admin.main_server_settings.index')
                ->withSuccess(trans('front.successfully_saved'));
    }

    public function newUserDefaultsSave() {
        $input = Request::all();

        try {
            if (! isset($input['enable_plans'])) {
                if (isset($input['enable_devices_limit']) && empty($input['devices_limit'])) {
                    throw new ValidationException([
                        'devices_limit' => strtr(
                            trans('validation.required'),
                            [':attribute' => trans('validation.attributes.devices_limit')])
                    ]);
                }

                if (isset($input['enable_subscription_expiration_after_days']) && empty($input['subscription_expiration_after_days'])) {
                    throw new ValidationException([
                        'subscription_expiration_after_days' => strtr(
                            trans('validation.required'),
                            [':attribute' => trans('validation.attributes.subscription_expiration_after_days')])
                    ]);
                }

                $this->adminNewUserDefaultsFormValidator->validate('update', $input);
            } else {
                if (empty($input['default_billing_plan'])) {
                    throw new ValidationException([
                        'default_billing_plan' => strtr(
                            trans('validation.required'),
                            [':attribute' => trans('validation.attributes.default_billing_plan')])
                    ]);
                }
            }

            //@TODO: dst settings validation

            $settings = settings('main_settings');

            $settings['devices_limit'] = !isset($input['enable_devices_limit']) ? NULL : $input['devices_limit'];
            $settings['subscription_expiration_after_days'] = !isset($input['enable_subscription_expiration_after_days']) ? NULL : $input['subscription_expiration_after_days'];

            $settings['allow_users_registration'] = boolval($input['allow_users_registration']);
            $settings['email_verification'] = boolval($input['email_verification']);
            $settings['enable_plans'] = isset($input['enable_plans']);
            $settings['default_billing_plan'] = isset($input['enable_plans']) ? $input['default_billing_plan'] : NULL;
            $settings['default_timezone'] = $input['default_timezone'];

            if (isset($input['default_dst_type'])) {
                $settings['default_dst_type'] = $input['default_dst_type'];
            }

            if (in_array($input['default_dst_type'] ?? null, ['exact', 'other', 'automatic'])) {
                switch($input['default_dst_type']) {
                    case 'exact':
                        $settings['default_dst_date_from'] = $input['default_dst_date_from'];
                        $settings['default_dst_date_to'] = $input['default_dst_date_to'];

                        break;
                    case 'other':
                        $settings['default_dst_month_from'] = $input['default_dst_month_from'];
                        $settings['default_dst_week_pos_from'] = $input['default_dst_week_pos_from'];
                        $settings['default_dst_week_day_from'] = $input['default_dst_week_day_from'];
                        $settings['default_dst_time_from'] = $input['default_dst_time_from'];
                        $settings['default_dst_month_to'] = $input['default_dst_month_to'];
                        $settings['default_dst_week_pos_to'] = $input['default_dst_week_pos_to'];
                        $settings['default_dst_week_day_to'] = $input['default_dst_week_day_to'];
                        $settings['default_dst_time_to'] = $input['default_dst_time_to'];

                        break;
                    case 'automatic':
                        $settings['default_dst_country_id'] = $input['default_dst_country_id'];

                        break;
                    default:
                        break;
                }
            }

            $settings['user_permissions'] = [];

            if (array_key_exists('perms', $input)) {
                $permissions = LaravelConfig::get('tobuli.permissions');

                foreach ($permissions as $key => $val) {
                    if (! array_key_exists($key, $input['perms'])) {
                        continue;
                    }

                    $settings['user_permissions'][$key] = [
                        'view' => $val['view'] && (Arr::get($input['perms'][$key], 'view') || Arr::get($input['perms'][$key], 'edit') || Arr::get($input['perms'][$key], 'remove')) ? 1 : 0,
                        'edit' => $val['edit'] && Arr::get($input['perms'][$key], 'edit') ? 1 : 0,
                        'remove' => $val['remove'] && Arr::get($input['perms'][$key], 'remove') ? 1 : 0
                    ];
                }
            }

            $currentBillingPlan = settings('main_settings.default_billing_plan');
            $newBillingPlan     = isset($input['enable_plans']) ? $input['default_billing_plan'] : NULL;

            settings('main_settings', $settings);

            if ($currentBillingPlan != $newBillingPlan) {
                updateUsersBillingPlan($currentBillingPlan, $newBillingPlan);
            }

            return Redirect::route('admin.billing.index')->withSuccess(trans('front.successfully_saved'));
        } catch (ValidationException $e) {
            return Redirect::route('admin.billing.index')->withUserDefaultsErrors($e->getErrors());
        }
    }

    /**
     * Deletes (flushes) all geocoder cache
     * @return mixed
     */
    public function deleteGeocoderCache() {
        $redirect = Redirect::route('admin.main_server_settings.index');

        try {
            \CustomFacades\GeoLocation::flushCache();
        } catch (\Exception $e) {
            return $redirect->withError(trans('admin.geocoder_cache_flush_fail'));
        }

        return $redirect->withSuccess(trans('admin.geocoder_cache_flush_success'));
    }
}
