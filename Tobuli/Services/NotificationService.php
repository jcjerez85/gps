<?php

namespace Tobuli\Services;

use App\Notifications\PopupNotification;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Entities\Popup;
use Tobuli\Entities\PopupRule;
use Tobuli\Entities\User;
use Tobuli\Popups\Rules\BaseRule;
use Tobuli\Popups\Rules\BillingPlan;
use Tobuli\Popups\Rules\DemoUser;
use Tobuli\Popups\Rules\FirstLogin;
use Tobuli\Popups\Rules\SubscriptionEnding;
use Tobuli\Popups\Rules\UserCreatedBefore;

class NotificationService
{
    /**
     * Available rules for user notifications
     *
     * @var array
     */
    public static $ruleCollection = [
        BillingPlan::class,
        DemoUser::class,
        SubscriptionEnding::class,
        FirstLogin::class,
        UserCreatedBefore::class,
    ];

    public function __construct() {}

    public function fill(array $input, User $user)
    {
        $popup = empty($input['id'])
            ? new Popup()
            : Popup::userControllable($user)->findOrFail($input['id']);

        $validator = Validator::make($input, [
            'active' => 'boolean',
            'name'   => 'required|string',
            'rules'  => 'array',
            'show_every_days' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->messages());
        }

        if (!$popup->exists && !$user->isAdmin()) {
            $popup->user_id = $user->id;
        }

        $popup->active = isset($input['active']);
        $popup->name = $input['name'];
        $popup->title = $input['title'];
        $popup->content = $input['content'];
        $popup->position = $input['position'];
        $popup->show_every_days = is_numeric($input['show_every_days']) ? $input['show_every_days'] : null;

        return $popup;
    }

    public function save(array $input, User $user): bool
    {
        $popup = $this->fill($input, $user);

        try {
            $popup->save();

            $popup->rules()->delete();

            $rules = $input['rules'];

            foreach ($rules as $ruleName => $values) {
                if ( ! isset($values['is_active']))
                    continue;

                unset($values['is_active']);

                foreach ($values as $key => $value) {
                    $rule = PopupRule::firstOrNew(['field_name' => $key, 'popup_id' => $popup->id, 'rule_name' => (string) $ruleName]);

                    $rule->field_value = $value;

                    $rule->save();

                    $popup->rules()->save($rule);
                }
            }

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function getPopups($user) {
        $popups = [];
        $all = Popup::userAccessible($user)->where('active', '=', true)->with('rules')->get();

        foreach ($all as $popup) {
            if ( ! $this->checkRules($popup, $user)) {
                continue;
            }

            $popups[] = $this->applyOnContent($popup, $user);
        }

        return $popups;
    }

    public function check($user)
    {
        $popups = $this->getPopups($user);

        foreach ($popups as $popup) {
            $this->sendNotification($popup, $user);
        }

        return true;
    }

    public function checkRules(Popup $popup, $user) {
        foreach ($popup->rules as $ruleContent) {

            $rule = BaseRule::load($ruleContent, $user);

            if ( ! $rule)
                continue;

            if ( ! $rule->doesApply()) {
                return false;
            }
        }

        return true;
    }

    public function applyOnContent(Popup $popup, $user)
    {
        foreach ($popup->rules as $ruleContent)
        {
            $rule = BaseRule::load($ruleContent, $user);
            if ( ! $rule)
                continue;

            $popup->title   = $rule->processShortcodes($popup->title);
            $popup->content = $rule->processShortcodes($popup->content);
        }

        return $popup;
    }


    public function sendNotification(Popup $popup, $user)
    {
        if ( ! $user)  return false;

        $exists = DatabaseNotification::where('data', '=', $popup->toJson())
            ->where('notifiable_id', '=', $user->id)
            ->where('type', '=', PopupNotification::class)
            ->when(!is_null($popup->show_every_days), function($query) use ($popup) {
                $query->where(function($q) use ($popup) {
                    $q->where('read_at',  '>', Carbon::now()->subDays($popup->show_every_days));
                    $q->orWhereNull('read_at');
                });
            })
            ->first();

        if ($exists) { return false; }

        $notification = new PopupNotification($popup);

        try {
            Notification::send($user, $notification);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }


}