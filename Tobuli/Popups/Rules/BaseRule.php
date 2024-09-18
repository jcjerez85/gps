<?php
/**
 * Created by PhpStorm.
 * User: antanas
 * Date: 18.2.15
 * Time: 14.55
 */

namespace Tobuli\Popups\Rules;

use Collective\Html\FormFacade;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\User;
use Tobuli\Entities\PopupRule;

class BaseRule implements RuleInterface
{
    protected $rule, $user;

    public  $shortcodes = [
        '{user_email}' => 'getUserEmail',
        '{user_email_base64}' => 'getUserEmailBase64'
    ];

    public function getUserEmail()
    {
        if ($this->user) {
            return $this->user->email;
        }

        return null;
    }

    public function getUserEmailBase64()
    {
        if ($this->user) {
            return base64_encode($this->user->email);
        }

        return null;
    }

    public function getActiveField($isActive = null)
    {
        return [
            '<div class="checkbox-inline">',
            FormFacade::checkbox(
                'rules['.get_class($this).'][is_active]',
                1 ,
                $isActive ? 1 : null,
                [
                    'data-disabler' => '.form-control[name^="rules['.addslashes(get_class($this)).']"];disable'
                ]
            ),
            FormFacade::label(null, null),
            '</div>'
        ];
    }


    public function __construct($ruleInstance = null, $user = null)
    {
        $this->rule = $ruleInstance;
        $this->user = $user;

        $parentVars = get_class_vars(__CLASS__);
        $this->shortcodes = array_merge($parentVars['shortcodes'], $this->shortcodes);
    }

    public static function load(PopupRule $rule,  $user = null) {
        $class = $rule->rule_name;

        if ( ! class_exists($class))
            return false;

        return new $class($rule, $user);
    }

    public function getFields()
    {
        return [];
    }

    public function doesApply()
    {
        return false;
    }

    public function processShortcodes($text) {

        foreach ($this->shortcodes as $shortcode=>$function) {
            $text = str_replace($shortcode, $this->{$function}(), $text);
        }

        return $text;
    }
}