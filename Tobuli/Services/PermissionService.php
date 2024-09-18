<?php namespace Tobuli\Services;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Tobuli\Entities\BillingPlan;

class PermissionService
{
    const GROUP_ADMIN = 1;
    const GROUP_USER = 2;
    const GROUP_MANAGER = 3;
    const GROUP_DEMO = 4;
    const GROUP_OPERATOR = 5;
    const GROUP_SUPERVISOR = 6;

    public function addToAll($name)
    {
        $permissions = config()->get('tobuli.permissions');
        $permission = $permissions[$name];

        $fields = [
            "id",
            "'$name' AS name",
            "{$permission['view']} AS view",
            "{$permission['edit']} AS edit",
            "{$permission['remove']} AS remove",
        ];

        DB::insert('INSERT INTO user_permissions (user_id, name, view, edit, remove) '
            . DB::table('users')
                ->select(DB::raw(implode(', ', $fields)))
                ->whereNull('billing_plan_id')
                ->toSql()
        );

        DB::insert('INSERT INTO billing_plan_permissions (plan_id, name, view, edit, remove) '
            . DB::table('billing_plans')
                ->select(DB::raw(implode(', ', $fields)))
                ->toSql()
        );

        $userPermissions = settings('main_settings.user_permissions');
        if ($userPermissions && empty($userPermissions[$name]))
        {
            $userPermissions[$name] = $permission;

            settings('main_settings.user_permissions', $userPermissions);
        }
    }

    public function filter(array $available, array $preferred)
    {
        $permissions = array_filter($available, function ($val, $key) use ($preferred) {
            return array_key_exists($key, $preferred);
        }, ARRAY_FILTER_USE_BOTH);

        array_walk($permissions, function(&$value, $key) use ($preferred) {
            $value['view'] = $value['view'] && (
                Arr::get($preferred[$key], 'view') ||
                Arr::get($preferred[$key], 'edit') ||
                Arr::get($preferred[$key], 'remove')
            ) ? 1 : 0;
            $value['edit'] = $value['edit'] && Arr::get($preferred[$key], 'edit') ? 1 : 0;
            $value['remove'] = $value['remove'] && Arr::get($preferred[$key], 'remove') ? 1 : 0;
        });

        return $permissions;
    }

    public function getByUser($user, $preferred = null)
    {
        $permissions = $this->getList();

        $user->load('manager');

        $permissions = $this->applyManagerPermissions($user, $permissions);
        $permissions = $this->applyUserRestrictions($user, $permissions);

        if (is_null($preferred))
            return $permissions;

        return $this->filter($permissions, $preferred);
    }

    public function getByGroupId($group_id)
    {
        $permissions = $this->getList();

        $role = $this->getGroupRole($group_id);

        return $this->applyRoleRestrictions($role, $permissions);
    }

    public function getByUserRole($user = null)
    {
        $permissions = $this->getByGroupId(self::GROUP_USER);

        if (is_null($user))
            return $permissions;

        return $this->applyUserOwnRestrictions($user, $permissions);
    }

    public function getByManagerRole()
    {
        return $this->getByGroupId(self::GROUP_MANAGER);
    }

    public function group($permissions)
    {
        $grouped = [];

        foreach ($permissions as $key => $val) {
            $parts = explode('.', $key);
            $entity = isset($parts[1]) ? $parts[0] : 'main';

            $grouped[$entity][$key] = $val;
        }

        return $grouped;
    }

    public function getUserDefaults()
    {
        if ( ! settings('main_settings.enable_plans'))
            return settings('main_settings.user_permissions');

        return $this->defaultPlanPermissions();
    }

    public function getGroupDefaults($group_id)
    {
        if ($group_id == self::GROUP_ADMIN)
            return $this->getList();

        return $this->getUserDefaults();
    }

    private function getList()
    {
        $permissions = config('tobuli.permissions');

        if ( ! settings('plugins.additional_installation_fields.status')) {
            unset(
                $permissions['device.installation_date'],
                $permissions['device.sim_activation_date'],
                $permissions['device.sim_expiration_date']
            );
        }

        if ( ! config('addon.device_authentication_field')) {
            unset($permissions['device.authentication']);
        }

        if (! config('addon.checklists')) {
            unset($permissions['checklist'],
                $permissions['checklist_activity'],
                $permissions['checklist_template'],
                $permissions['checklist_qr_code'],
                $permissions['checklist_qr_pre_start_only'],
                $permissions['checklist_optional_image']);
        }

        if (! settings('plugins.call_actions.status')) {
            unset($permissions['call_actions']);
        }

        if (! settings('plugins.beacons.status')) {
            unset($permissions['beacons']);
        }

        if (! settings('plugins.business_private_drive.status')) {
            unset($permissions['device_route_types']);
        }

        if (! settings('plugins.sim_blocking.status')) {
            unset($permissions['device.msisdn']);
        }

        if (! config('addon.widget_template')) {
            unset($permissions['widget_template_webhook']);
        }

        if (! config('addon.custom_device_add')) {
            unset($permissions['custom_device_add']);
        }

        if (! config('addon.device_type')) {
            unset($permissions['device.device_type_id']);
        }

        if (! config('addon.custom_fields')) {
            unset($permissions['device.custom_fields']);
        }

        if (! config('addon.media_categories')) {
            unset($permissions['media_categories']);
        }

        if (!config('addon.external_url')) {
            unset($permissions['external_url']);
        }

        if (!config('addon.login_token')) {
            unset($permissions['user.login_token']);
        }

        if (!config('addon.forwards')) {
            unset($permissions['forwards']);
        }

        if (!settings('login_periods.enabled')) {
            unset($permissions['user.login_periods']);
        }

        return $permissions;
    }

    private function applyManagerPermissions($user, $permissions)
    {
        if (is_null($manager = $user->manager))
            return $permissions;

        return $this->applyUserOwnRestrictions($manager, $permissions);
    }

    private function applyUserOwnRestrictions($user, $permissions)
    {
        foreach ($permissions as $permission => $modes) {
            $permissions[$permission]['view'] = $modes['view'] && $user->perm($permission, 'view');
            $permissions[$permission]['edit'] = $modes['edit'] && $user->perm($permission, 'edit');
            $permissions[$permission]['remove'] = $modes['remove'] && $user->perm($permission, 'remove');
        }

        return $permissions;
    }

    private function applyUserRestrictions($user, $permissions)
    {
        $role = $this->getUserRole($user);

        return $this->applyRoleRestrictions($role, $permissions);
    }

    private function applyRoleRestrictions($role, $permissions)
    {
        $restricted_permissions = config("tobuli.restricted_permissions.$role");

        if (is_null($restricted_permissions))
            return $permissions;

        return $this->applyRestrictions($permissions, $restricted_permissions);
    }

    private function applyRestrictions($permissions, $restricted)
    {
        foreach ($restricted as $permission => $modes) {
            foreach ($modes as $key => $value)
                $permissions[$permission][$key] = $permissions[$permission][$key] && $value;
        }

        return $permissions;
    }

    private function getUserRole($user)
    {
        return $this->getGroupRole($user->group_id);
    }

    private function getGroupRole($group_id)
    {
        switch ($group_id) {
            case self::GROUP_ADMIN:
                $role = 'admin';
                break;
            case self::GROUP_USER:
                $role = 'user';
                break;
            case self::GROUP_MANAGER:
            case self::GROUP_OPERATOR:
            case self::GROUP_SUPERVISOR:
                $role = 'manager';
                break;
            case self::GROUP_DEMO:
                $role = 'demo';
                break;
            default:
                throw new \Exception('User group not found!');
                break;
        }

        return $role;
    }

    private function defaultPlanPermissions()
    {
        $plan = BillingPlan::find(settings('main_settings.default_billing_plan'));

        return $plan->getPermissions();
    }
}