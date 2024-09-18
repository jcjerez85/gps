<?php

namespace App\Exceptions;

use App\Policies\PoiGroupPolicy;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tobuli\Entities\Alert;
use Tobuli\Entities\Chat;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceExpense;
use Tobuli\Entities\DeviceGroup;
use Tobuli\Entities\DeviceRouteType;
use Tobuli\Entities\EmailTemplate;
use Tobuli\Entities\Event;
use Tobuli\Entities\EventCustom;
use Tobuli\Entities\Forward;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\MediaCategory;
use Tobuli\Entities\Order;
use Tobuli\Entities\PoiGroup;
use Tobuli\Entities\Report;
use Tobuli\Entities\Route;
use Tobuli\Entities\SmsTemplate;
use Tobuli\Entities\Subscription;
use Tobuli\Entities\Task;
use Tobuli\Entities\User;
use Tobuli\Entities\UserDriver;
use Tobuli\Entities\Poi;
use Tobuli\Entities\DeviceCamera;
use Tobuli\Entities\Sharing;
use Tobuli\Entities\ChecklistTemplate;
use Tobuli\Entities\Checklist;
use Tobuli\Entities\ChecklistRow;
use Tobuli\Entities\CallAction;
use Tobuli\Entities\CustomField;
use Tobuli\Entities\UserGprsTemplate;
use Tobuli\Entities\UserSmsTemplate;

class Manager
{
    protected $user;

    protected $permissionMap;

    public function __construct($user)
    {
        $this->user = $user;

        $this->permissionMap = [
            'show'   => 'edit',
            'view'   => 'view',
            'create' => 'edit',
            'store'  => 'edit',
            'edit'   => 'edit',
            'update' => 'edit',
            'remove' => 'remove',
            'active' => 'edit',
            'clean'  => 'remove',
            'enable' => 'edit',
            'disable' => 'edit'
        ];

        $this->modelMap = [
            'alerts'                      => Alert::class,
            'devices'                     => Device::class,
            'devices_groups'              => DeviceGroup::class,
            'custom_events'               => EventCustom::class,
            'geofences'                   => Geofence::class,
            'poi'                         => Poi::class,
            'pois_groups'                 => PoiGroup::class,
            'events'                      => Event::class,
            'reports'                     => Report::class,
            'routes'                      => Route::class,
            'drivers'                     => UserDriver::class,
            'tasks'                       => Task::class,
            'chats'                       => Chat::class,
            'users'                       => User::class,
            'orders'                      => Order::class,
            'forwards'                    => Forward::class,
            'device_camera'               => DeviceCamera::class,
            'device_route_types'          => DeviceRouteType::class,
            'device_expenses'             => DeviceExpense::class,
            'sharing'                     => Sharing::class,
            'call_actions'                => CallAction::class,
            'custom_field'                => CustomField::class,
            'subscriptions'               => Subscription::class,
            'email_templates'             => EmailTemplate::class,
            'sms_templates'               => SmsTemplate::class,
            'user_sms_templates'          => UserSmsTemplate::class,
            'user_gprs_templates'         => UserGprsTemplate::class,
            'media_categories'            => MediaCategory::class,
            'checklist'                   => Checklist::class,
            'checklist_activity'          => ChecklistRow::class,
            'checklist_template'          => ChecklistTemplate::class,
            'checklist_qr_code'           => null,
            'checklist_qr_pre_start_only' => null,
            'checklist_optional_image'    => null,
            'camera'                      => null,
            'history'                     => null,
            'send_command'                => null,
            'device_configuration'        => null,
        ];
    }

    public function check($repo, $action, $model = null)
    {
        switch ($action) {
            case 'show':
            case 'edit':
            case 'update':
            case 'remove':
            case 'active':
            case 'enable':
            case 'disable':
            case 'own':
                if (empty($model) && $this->getModelClass($repo))
                    throw new ResourseNotFoundException($this->getModelTrans($repo));
                break;
            case 'view':
            case 'create':
            case 'store':
            case 'clean':
                $model = $this->getModel($repo);
                break;
        }

        if (is_null($model)) {
            if ( ! $this->user->perm($repo, $this->permissionMap[$action]))
                throw new PermissionException();
        } else {
            //dd($model);
            if ( ! $this->user->can($action, $model))
                throw new PermissionException();
        }
    }

    protected function getModel($repo)
    {
        $class = $this->getModelClass($repo);

        if ($class)
            return new $class();

        return null;
    }

    protected function getModelClass($repo)
    {
        if ( ! Arr::has($this->modelMap, $repo))
            throw new \Exception('No model class declared');

        return Arr::get($this->modelMap, $repo);
    }

    protected function getModelTrans($repo)
    {
        switch ($repo) {
            case 'custom_events':
                return "front.event";
            case 'reports':
                return "front.report";
            case 'routes':
                return "front.routes";
            case 'poi':
                return "front.marker";
            case 'drivers':
                return "front.driver";
            case 'device_camera':
                return 'front.device_camera';
            case 'checklist':
                return 'front.checklist';
            case 'checklist_template':
                return 'front.checklist_template';
            default:
                $singular = Str::singular($repo);

                return "global.$singular";
        }
    }
}