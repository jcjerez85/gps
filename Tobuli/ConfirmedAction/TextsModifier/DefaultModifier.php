<?php

namespace Tobuli\ConfirmedAction\TextsModifier;

use Illuminate\Http\Request;

class DefaultModifier implements TextsModifierInterface
{
    public function modify(array &$texts, Request $request)
    {
        if (!isset($texts['title'])) {
            $texts['title'] = trans('admin.confirm');
        }

        if (!isset($texts['description'])) {
            $texts['description'] = trans('global.please_confirm_action');
        }
    }
}