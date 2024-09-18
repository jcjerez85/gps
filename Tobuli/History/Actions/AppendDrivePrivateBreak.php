<?php

namespace Tobuli\History\Actions;


class AppendDrivePrivateBreak extends ActionBreak
{
    static public function required()
    {
        return [
            AppendDrivePrivate::class,
        ];
    }

    public function boot() {}

    protected function isBreakable($position)
    {
        return empty($position->drive_private);
    }
}