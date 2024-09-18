<?php

namespace Tobuli\ConfirmedAction\TextsModifier;

use Illuminate\Http\Request;

interface TextsModifierInterface
{
    public function modify(array &$texts, Request $request);
}