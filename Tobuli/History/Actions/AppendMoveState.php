<?php

namespace Tobuli\History\Actions;


class AppendMoveState extends ActionAppend
{
    const MOVING  = true;
    const STOPED  = false;

    protected $stop_speed;
    protected $stop_seconds;

    static public function required()
    {
        return [
            AppendPosition::class,
            AppendDuration::class,
            AppendEngineStatus::class,
            AppendSpeed::class,
        ];
    }

    public function boot()
    {
        $this->stop_speed = $this->history->config('stop_speed');
        $this->stop_seconds = $this->history->config('stop_seconds');
    }

    public function proccess(&$position)
    {
        $this->setState($position);

        if (is_null($position->moving))
        {
            $this->addList($position);
            $this->proceed();

            return;
        }

        $previous = $this->getPrevPosition();

        if ($previous && is_null($previous->moving))
        {
            $this->history->processList(function($listPosition) use ($position) {
                if (!isset($listPosition->moving)) {
                    $listPosition->moving = $this->getStatePrev($position);
                }

                if (is_null($listPosition->moving)) {
                    $listPosition->moving = $this->getStatePrev($position);
                }

                return $listPosition;
            });

            $this->doitList();
        }
    }

    protected function isMoving($position)
    {
        return $position->engine && $position->speed > $this->stop_speed;
    }

    protected function setState(& $position)
    {
        if (isset($position->moving))
            return;

        $position->moved_at = $this->getMovedAt($position);
        $position->moving = $this->getState($position);
    }

    protected function getMovedAt($position)
    {
        if ($this->isMoving($position))
            return $position->timestamp;

        $previous = $this->getPrevPosition();

        if ( ! $previous)
            return null;

        if ($this->isMoving($previous) && $position->duration <= $this->stop_seconds)
            return $position->timestamp;

        return $previous->moved_at ?? null;
    }

    protected function getState($position)
    {
        if ($this->isMoving($position))
            return self::MOVING;

        $previous = $this->getPrevPosition();

        if ( ! $previous)
            return self::STOPED;

        if (self::STOPED === $previous->moving)
            return self::STOPED;

        if (is_null($position->moved_at))
            return self::STOPED;

        if ($position->timestamp - $position->moved_at > $this->stop_seconds)
            return self::STOPED;

        return null;
    }

    protected function getStatePrev($position)
    {
        if ($position->duration > $this->stop_seconds)
            return self::STOPED;

        return $position->moving;
    }
}