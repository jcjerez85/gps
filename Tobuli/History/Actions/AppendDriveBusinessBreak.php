<?php

namespace Tobuli\History\Actions;


class AppendDriveBusinessBreak extends ActionBreak
{
    static public function required()
    {
        return [
            AppendDriveBusiness::class,
        ];
    }

    public function boot() {}

    protected function isBreakable($position)
    {
        return empty($position->drive_business);
    }
}