<?php

namespace Tobuli\History\Actions;


use Tobuli\History\Group;

class GroupDriveBusinessPrivate extends ActionGroup
{
    const STATE_DEFAULT  = null;
    const STATE_BUSINESS = 1;
    const STATE_PRIVATE  = 2;

    protected $moving = null;
    protected $state  = self::STATE_DEFAULT;

    static public function required()
    {
        return [
            AppendMoveState::class,
            AppendDriveBusiness::class,
            AppendDrivePrivate::class,
        ];
    }

    public function boot() {}

    public function proccess($position)
    {
        if ($this->isChanged($position))
            $this->onChange($position);

        $this->state = $this->getState($position);
        $this->moving = $position->moving;
    }

    protected function isChanged($position)
    {
        if ($this->moving !== $position->moving)
            return true;

        if ($this->state !== $this->getState($position))
            return true;

        return false;
    }

    protected function onChange($position)
    {
        if (!$position->moving) {
            $this->history->groupEnd('drive', $position);
            $this->history->groupEnd('drive_business', $position);
            $this->history->groupEnd('drive_private', $position);

            return;
        }

        switch ($this->getState($position)) {
            case self::STATE_BUSINESS:
                $this->history->groupEnd('drive', $position);
                $this->history->groupEnd('drive_private', $position);
                $this->history->groupStart('drive_business', $position);
                break;
            case self::STATE_PRIVATE:
                $this->history->groupEnd('drive', $position);
                $this->history->groupEnd('drive_business', $position);
                $this->history->groupStart('drive_private', $position);
                break;
            default:
                $this->history->groupEnd('drive_private', $position);
                $this->history->groupEnd('drive_business', $position);
                $this->history->groupStart('drive', $position);
                break;
        }
    }

    protected function getState($position)
    {
        if (!empty($position->drive_business))
            return self::STATE_BUSINESS;

        if (!empty($position->drive_private))
            return self::STATE_PRIVATE;

        return self::STATE_DEFAULT;
    }
}