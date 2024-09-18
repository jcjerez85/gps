<?php


namespace Tobuli\Services\EntityLoader;


use Tobuli\Entities\User;

class UserDevicesLoader extends DevicesLoader
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->setQueryItems(
            $this->user->devices()->orderBy('devices.name', 'asc')
        );
    }
}