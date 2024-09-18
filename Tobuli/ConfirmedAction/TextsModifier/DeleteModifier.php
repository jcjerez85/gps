<?php

namespace Tobuli\ConfirmedAction\TextsModifier;

use Illuminate\Http\Request;

class DeleteModifier implements TextsModifierInterface
{
    public function modify(array &$texts, Request $request)
    {
        if ($request->method() !== Request::METHOD_DELETE) {
            return;
        }

        if (!isset($texts['title'])) {
            $texts['title'] = trans('global.delete');
        }

        if (!isset($texts['description'])) {
            $texts['description'] = trans('admin.do_delete');
        }
    }
}