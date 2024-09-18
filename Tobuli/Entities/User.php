<?php

namespace Tobuli\Entities;

use App\Events\UserPasswordChanged;
use App\Jobs\SendEmailJob;
use App\Notifications\ResetPassword;
use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config as LaravelConfig;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Tobuli\Helpers\Settings\Settingable;
use Tobuli\Services\NotificationService;
use Tobuli\Traits\Chattable;
use Tobuli\Traits\Customizable;
use Tobuli\Traits\EventLoggable;
use Tobuli\Traits\FcmTokensTrait;
use Tobuli\Traits\Filterable;
use Tobuli\Traits\Searchable;
use Tobuli\Traits\SentCommandActor;

class User extends AbstractEntity implements
    AuthenticatableContract,
    CanResetPasswordContract,
    FcmTokenableInterface,
    ChattableInterface,
    SecondaryCredentialsInterface
{
    const VERIFY_EMAIL_TIMEOUT_MIN = 5;

    use Authenticatable, CanResetPassword, Settingable, Notifiable, Chattable,
        EventLoggable, SentCommandActor, Searchable, Filterable, Customizable,
        FcmTokensTrait;

    private $searchable = [
        'email',
    ];
    private $filterables = [
        'email',
    ];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('remember_token', 'api_hash', 'password', 'login_token');

    protected $fillable = array(
        'id',
        'active',
        'password',
        'group_id',
        'manager_id',
        'billing_plan_id',
        'map_id',
        'email',
        'devices_limit',
        'subscription_expiration',
        'loged_at',
        'lang',
        'unit_of_distance',
        'unit_of_capacity',
        'unit_of_altitude',
        'duration_format',
        'timezone_id',
        'sms_gateway',
        'sms_gateway_url',
        'api_hash',
        'api_hash_expire',
        'available_maps',
        'sms_gateway_params',
        'api_hash',
        'sms_gateway_app_date',
        'ungrouped_open',
        'week_start_day',
        'top_toolbar_open',
        'map_controls',
        'phone_number',
        'client_id',
        'company_id',
        'login_periods',
    );

    protected $casts = [
        'id' => 'integer',
        'active' => 'integer',
        'group_id' => 'integer',
        'manager_id' => 'integer',
        'billing_plan_id' => 'integer',
        'map_id' => 'integer',
        'devices_limit' => 'integer',
        'timezone_id' => 'integer',
        'login_periods' => 'array',
        'ungrouped_open' => 'array',
    ];

    private $permissions = NULL;

    private ?UserSecondaryCredentials $loginSecondaryCredentials = null;

    protected static function boot()
    {
        parent::boot();

        // if ( Auth::check() && ! Auth::user()->isGod())
            // static::addGlobalScope(new \Tobuli\Scopes\GodUserScope());

        static::saving(function ($user) {
            if ($user->isDirty('password')) {
                while(self::where([
                    'api_hash' => $hash = Hash::make("{$user->email}:{$user->password}")
                ])->first());
                $user->api_hash = $hash;
                $user->remember_token = null;
            }
        });

        static::creating(function (User $user) {
            if (!settings('main_settings.email_verification')) {
                $user->email_verified_at = date('Y-m-d H:i:s');
            }
        });

        static::created(function (User $user) {
            if (settings('main_settings.email_verification') && !$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }
        });
    }

    public function getPasswordHashAttribute()
    {
        return md5($this->loginSecondaryCredentials->password ?? $this->password);
    }

    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        $this->attributes['password'] = Hash::make($value);

        event(new UserPasswordChanged($this));
    }

    public function setAvailableMapsAttribute($value)
    {
        $this->attributes['available_maps'] = serialize(array_values($value));
    }

    public function setSmsGatewayParamsAttribute($value)
    {
        $this->attributes['sms_gateway_params'] = serialize($value);
    }

    public function setCompanyIdAttribute($value)
    {
        if (empty($value))
            $value = null;

        $this->attributes['company_id'] = $value;
    }

    public function setManagerIdAttribute($value)
    {
        if (empty($value))
            $value = null;

        $this->attributes['manager_id'] = $value;
    }

    public function setBillingPlanIdAttribute($value)
    {
        if (empty($value))
            $value = null;

        $this->attributes['billing_plan_id'] = $value;
    }

    public function setExpirationDateAttribute($value)
    {
        $this->attributes['subscription_expiration'] = $value;
    }

    //to keep existing functionality
    public function getUserTimezoneAttribute()
    {
        return Cache::store('array')
            ->rememberForever("users.{$this->id}.timezone", function () {
                if (! $this->relationLoaded('timezone')) {
                    $this->load('timezone');
                }

                return $this->getRelation('timezone') ?: new Timezone();
            });
    }

    public function getAvailableMapsAttribute($value)
    {
        try {
            $list = unserialize($value);
            $maps = array_keys(getAvailableMaps());

            $list = array_filter($list, function($id) use ($maps) {
                return in_array($id, $maps);
            });

            return array_values($list);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getSmsGatewayParamsAttribute($value)
    {
        return unserialize($value);
    }

    public function getUnitOfSpeedAttribute() {
        return trans("front.dis_h_{$this->unit_of_distance}");
    }

    public function getDistanceUnitHourAttribute() {
        return $this->unit_of_speed;
    }

    public function getWeekStartWeekdayAttribute($value)
    {
        $weekdays = [
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        ];

        return Arr::get($weekdays, $this->week_start_day, 'monday');
    }

    public function getPlanIdAttribute()
    {
        return $this->billing_plan_id;
    }

    public function getExpirationDateAttribute()
    {
        return $this->subscription_expiration;
    }

    public function timezone() {
        return $this->hasOne('Tobuli\Entities\Timezone', 'id', 'timezone_id');
    }

    public function manager() {
        return $this->hasOne('Tobuli\Entities\User', 'id', 'manager_id');
    }

    public function billing_plan() {
        return $this->hasOne('Tobuli\Entities\BillingPlan', 'id', 'billing_plan_id');
    }

    public function alerts() {
        return $this->hasMany('Tobuli\Entities\Alert', 'user_id', 'id');
    }

    public function secondaryCredentials(): HasMany
    {
        return $this->hasMany(UserSecondaryCredentials::class);
    }

    public function accessibleDevices() {
        if ($this->isAdmin() || $this->isSupervisor()) {
            $relation = $this->hasMany('Tobuli\Entities\Device', 'user_id', 'id')
                ->orWhere(function ($query) {
                    $query->whereNull('user_id')->orWhere('user_id', '>', 0);
                });
        } elseif ($this->isManager()) {

            $self = $this;

            $relation = $this->hasMany('Tobuli\Entities\Device', 'user_id', 'id')
                ->select('devices.*')
                ->orWhere(function ($query) {
                    $query->whereNull('devices.user_id')->orWhere('devices.user_id', '>', 0);
                })
                ->join('user_device_pivot', 'user_device_pivot.device_id', '=', 'devices.id')
                ->whereIn('user_device_pivot.user_id', function ($query) use ($self) {
                    $query
                        ->select('users.id')
                        ->from('users')
                        ->where('users.id', $self->id)
                        ->orWhere('users.manager_id', $self->id)
                    ;
                })
                ->distinct('devices.id')
            ;
        } else {
            $relation = $this->belongsToMany('Tobuli\Entities\Device', 'user_device_pivot', 'user_id', 'device_id');
        }

        return $relation->orderBy('devices.name', 'asc');
    }

    public function accessibleDevicesWithGroups()
    {
        if ($this->isAdmin() || $this->isSupervisor()) {
            return $this->hasMany('Tobuli\Entities\Device', 'user_id', 'id')
                ->select('devices.*', 'user_device_pivot.group_id')
                ->orWhere(function ($query) {
                    $query->whereNull('devices.user_id')->orWhere('devices.user_id', '>', 0);
                })
                ->leftJoin('user_device_pivot', function ($join) {
                    $join->on('user_device_pivot.device_id', '=', 'devices.id')
                        ->where('user_device_pivot.user_id', '=', $this->id);
                })
                ->orderBy('devices.name', 'asc');
        }

        if ($this->isManager()) {
            return $this->hasMany('Tobuli\Entities\Device', 'user_id', 'id')
                ->select('devices.*', 'user_device_group.group_id')
                ->orWhere(function ($query) {
                    $query->whereNull('devices.user_id')->orWhere('devices.user_id', '>', 0);
                })
                ->join('user_device_pivot', 'user_device_pivot.device_id', '=', 'devices.id')
                ->whereIn('user_device_pivot.user_id', function ($query) {
                    $query
                        ->select('users.id')
                        ->from('users')
                        ->where('users.id', $this->id)
                        ->orWhere('users.manager_id', $this->id)
                    ;
                })
                ->leftJoin('user_device_pivot as user_device_group', function ($join) {
                    $join->on('user_device_group.device_id', '=', 'devices.id')
                        ->where('user_device_group.user_id', '=', $this->id);
                })
                ->distinct('devices.id');
        }

        return $this->belongsToMany('Tobuli\Entities\Device', 'user_device_pivot', 'user_id', 'device_id')
            ->withPivot('group_id')
            ->orderBy('devices.name', 'asc');
    }

    public function devices() {
        return $this->belongsToMany('Tobuli\Entities\Device', 'user_device_pivot', 'user_id', 'device_id')
            ->withPivot(['group_id', 'current_driver_id', 'active'])
            ->orderBy('name', 'asc');
    }

    public function devices_sms() {
        return $this->belongsToMany('Tobuli\Entities\Device', 'user_device_pivot', 'user_id', 'device_id')
            ->where('sim_number', '!=', '')
            ->withPivot(['group_id'])
            ->orderBy('name', 'asc');
    }

    public function drivers() {
        return $this->hasMany('Tobuli\Entities\UserDriver', 'user_id', 'id');
    }

    public function subusers() {
        return $this->hasMany('Tobuli\Entities\User', 'manager_id', 'id');
    }

    public function sms_templates() {
        return $this->hasMany('Tobuli\Entities\UserSmsTemplate', 'user_id', 'id');
    }

    public function geofences() {
        return $this->hasMany('Tobuli\Entities\Geofence', 'user_id', 'id');
    }
    
    public function geofenceGroups() {
        return $this->hasMany('Tobuli\Entities\GeofenceGroup', 'user_id', 'id');
    }

    public function deviceGroups(): HasMany
    {
        return $this->hasMany(DeviceGroup::class);
    }

    public function pois() {
        return $this->hasMany('Tobuli\Entities\Poi', 'user_id', 'id');
    }

    public function poiGroups() {
        return $this->hasMany('Tobuli\Entities\PoiGroup', 'user_id', 'id');
    }

    public function routes(): HasMany
    {
        return $this->hasMany('Tobuli\Entities\Route');
    }

    public function forwards() {
        return $this->belongsToMany(Forward::class, 'user_forward', 'user_id', 'forward_id');
    }

    public function getPermissions()
    {
        $permissions = [];

        $defaultPermissions = LaravelConfig::get('tobuli.permissions');

        foreach ($defaultPermissions as $name => $modes) {
            foreach($modes as $mode => $value) {
                $permissions[$name][$mode] = $this->perm($name, $mode);
            }
        }

        return $permissions;
    }

    public function perm($name, $mode) {
        $mode = trim($mode);
        $modes = LaravelConfig::get('tobuli.permissions_modes');

        if (!array_key_exists($mode, $modes))
            die('Bad permission');

        if (is_null($this->permissions)) {
            $this->permissions = [];
            if (empty($this->billing_plan_id)) {
                $perms = DB::table('user_permissions')
                    ->select('name', 'view', 'edit', 'remove')
                    ->where('user_id', '=', $this->id)
                    ->get()
                    ->all();
            } else {
                $perms = DB::table('billing_plan_permissions')
                    ->select('name', 'view', 'edit', 'remove')
                    ->where('plan_id', '=', $this->billing_plan_id)
                    ->get()
                    ->all();
            }

            if (!empty($perms)) {
                $manager = $this->manager_id ? $this->manager : null;

                foreach ($perms as $perm) {
                    if ($manager) {
                        $this->permissions[$perm->name] = [
                            'view' => $perm->view && $manager->perm($perm->name, 'view'),
                            'edit' => $perm->edit && $manager->perm($perm->name, 'edit'),
                            'remove' => $perm->remove && $manager->perm($perm->name, 'remove')
                        ];
                    } else {
                        $this->permissions[$perm->name] = [
                            'view' => $perm->view,
                            'edit' => $perm->edit,
                            'remove' => $perm->remove
                        ];
                    }
                }
            }
        }

        return (array_key_exists($name, $this->permissions) && array_key_exists($mode, $this->permissions[$name])) ? boolval($this->permissions[$name][$mode]) : false;
    }

    public function getMapControlsAttribute($value)
    {
        return new \SettingsArray(json_decode($value, true));
    }

    public function setMapControlsAttribute($value)
    {
        $this->attributes['map_controls'] = json_encode($value);
    }

    public function isGod()
    {
        return $this->email == 'admin@server.com';
    }

    public function isAdmin()
    {
        return $this->group_id === 1;
    }

    public function isSupervisor()
    {
        return $this->group_id === 6;
    }

    public function isReseller()
    {
        return $this->group_id === 3;
    }

    public function isOperator()
    {
        return $this->group_id === 5;
    }

    public function isManager()
    {
        return $this->isReseller() || $this->isOperator() || $this->isSupervisor();
    }

    public function isDemo()
    {
        return $this->group_id === 4;
    }

    public function canChangeAppearance()
    {
        return $this->isReseller();
    }

    public static function getManagerTopFirst($user_id)
    {
        $user_id = intval($user_id);

        return \Illuminate\Support\Facades\Cache::remember("user_manage_{$user_id}", 120, function() use ($user_id){
            return self::fromQuery("
                SELECT T2.*
                FROM (
                       SELECT
                         @r AS _id,
                         (SELECT @r := IF(group_id <> 3,manager_id,null) FROM users WHERE id = _id) AS manager_id,
                         @l := @l + 1 AS lvl
                       FROM
                         (SELECT @r := ?, @l := 0) vars,
                         users WHERE @r <> 0) T1
                  JOIN users T2
                    ON T1._id = T2.id
                ORDER BY T1.lvl DESC
            ", [$user_id])->first();
        });
    }

    public function scopeDemo($query)
    {
        return $query->where('group_id', 4);
    }

    public function scopeOperator($query)
    {
        return $query->where('group_id', 5);
    }

    public function isActive()
    {
        return $this->active;
    }

    public function isCapable()
    {
        return $this->isActive() && !$this->isExpired();
    }

    public function hasExpiration()
    {
        if (empty($this->subscription_expiration))
            return false;

        if ($this->subscription_expiration == '0000-00-00 00:00:00')
            return false;

        return true;
    }

    public function isExpiredWithoutExtra()
    {
        if (!$this->hasExpiration())
            return false;

        if (strtotime($this->subscription_expiration) > time())
            return false;

        return true;
    }

    public function isExpired(): bool
    {
        if (!$this->isExpiredWithoutExtra()) {
            return false;
        }

        $extraTime = settings('main_settings.extra_expiration_time');

        return !$extraTime
            || \Carbon::parse($this->subscription_expiration)->addSeconds($extraTime)->getTimestamp() <= time();
    }

    public function isLoggedBefore()
    {
        if (empty($this->loged_at))
            return false;

        if ($this->loged_at == '0000-00-00 00:00:00')
            return false;

        return true;
    }
    
    public function canSendSMS()
    {
        if ( ! $this->perm('sms_gateway', 'view'))
            return false;

        if ( ! $this->sms_gateway)
            return false;

        return true;
    }

    public function can($ability, $entity, $property = null)
    {
        if (is_null($property)) {
            return policy($entity)->$ability($this, $entity);
        }

        return propertyPolicy($entity)->$ability($this, $entity, $property);
    }

    public function able($action)
    {
        return actionPolicy($action)
            ->able($this);
    }

    public function own($entity)
    {
        return policy($entity)->own($this, $entity);
    }

    public function hasDeviceLimit()
    {
        if ($this->isAdmin())
            return false;

        return ! is_null($this->devices_limit);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function commandSchedules()
    {
        return $this->hasMany(CommandSchedule::class)->with('schedule')->latest();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeIsExpiringAfter($query, int $days, bool $addExtra = true)
    {
        $dateFrom = Carbon::now();
        $dateTo = Carbon::now()->addDays($days);

        if ($addExtra && ($extraTime = settings('main_settings.extra_expiration_time'))) {
            $dateFrom->subSeconds($extraTime);
            $dateTo->subSeconds($extraTime);
        }

        return $query
            ->where('subscription_expiration', '!=', '0000-00-00 00:00:00')
            ->where('subscription_expiration', '>=', $dateFrom)
            ->where('subscription_expiration', '<=', $dateTo);
    }

    public function scopeIsExpiredBefore($query, int $days, bool $addExtra = true)
    {
        $date = Carbon::now()->subDays($days);

        if ($addExtra && ($extraTime = settings('main_settings.extra_expiration_time'))) {
            $date->subSeconds($extraTime);
        }

        return $query
            ->where('subscription_expiration', '!=', '0000-00-00 00:00:00')
            ->where('subscription_expiration', '<=', $date);
    }

    public function scopeUserAccessible(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isSupervisor()) {
            return $query;
        }

        if ($user->isManager()) {
            return $query->where(function($q) use ($user) {
                return $q->where('manager_id', $user->id)->orWhere('id', $user->id);
            });
        }

        return $query->where('id', $user->id);
    }

    public function filteredUnreadNotifications($filters = null)
    {
        $items = $this->unreadNotifications;

        if (! $filters) {
            $items->markAsRead();

            return $items;
        }

        $items = $items->filter(function($notification) use ($filters) {
            foreach ($filters as $field => $filterValue) {
                $equal = strpos($field, '!') !== 0;

                if (! $equal) {
                    $field = substr($field, 1);
                }

                $value = Arr::get($notification->toArray(), $field);

                if (! is_array($filterValue)) {
                    $filterValue = [$filterValue];
                }

                if ($equal XOR in_array($value, $filterValue)) {
                    return false;
                }
            }
            return true;
        });
        $items->markAsRead();

        return $items;
    }

    public function topBars()
    {
        $popups = (new NotificationService())->getPopups($this);

        return array_filter($popups, function($popup){
            return $popup['position'] == 'top';
        });
    }

    public function activate(BillingPlan $plan, $expirationDate)
    {
        $this->update([
            'billing_plan_id'         => $plan->id,
            'devices_limit'           => $plan->objects,
            'subscription_expiration' => $expirationDate,
        ]);
    }

    public function setExpirationDate($expirationDate)
    {
        $this->update([
            'subscription_expiration' => $expirationDate,
        ]);
    }

    public function renew($expirationDate)
    {
        $this->setExpirationDate($expirationDate);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        if ($emailTemplate = EmailTemplate::getTemplate('reset_password', $this)) {
            try {
                sendTemplateEmail($this->email, $emailTemplate, $token);
            } catch (\Exception $e) {
                Bugsnag::notifyException($e);
            }
        } else {
            $this->notify(new ResetPassword($token));
        }
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function markEmailAsVerified()
    {
        $this->email_verified_at = date('Y-m-d H:i:s');
        $this->save();
    }

    public function sendEmailVerificationNotification()
    {
        if (Cache::get('user_' . $this->id . '_verification_reminder')) {
            return;
        }

        /** @var EmailTemplate $emailTemplate */
        $emailTemplate = EmailTemplate::getTemplate('email_verification', $this);

        $token = sha1($this->email) . ';' . $this->id;

        $content = $emailTemplate->buildTemplate($token);

        dispatch(new SendEmailJob($this->email, $content['subject'], $content['body']));

        Cache::put('user_' . $this->id . '_verification_reminder', 60, self::VERIFY_EMAIL_TIMEOUT_MIN);
    }

    public function setLoginSecondaryCredentials(?UserSecondaryCredentials $credentials): self
    {
        $this->loginSecondaryCredentials = $credentials;

        return $this;
    }

    public function getLoginSecondaryCredentials(): ?UserSecondaryCredentials
    {
        return $this->loginSecondaryCredentials;
    }

    public function isMainLogin(): bool
    {
        return $this->loginSecondaryCredentials === null;
    }
}
