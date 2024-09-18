<?php

namespace Tobuli\History\Actions;

trait LoadTrait
{
    public static $loadStates = [1];
    public static $summarize = false;

    private static function getLoadStateName($state = null): string
    {
        if (static::$summarize || $state === null) {
            return 'loading_unloading';
        }

        return $state ? 'loading' : 'unloading';
    }

    private static function getPositionLoadStateName($position): string
    {
        return static::getLoadStateName($position->loadChange['state']);
    }

    private static function isPositionLoadValid($position): bool
    {
        return isset($position->loadChange) && in_array($position->loadChange['state'], static::$loadStates);
    }
}
