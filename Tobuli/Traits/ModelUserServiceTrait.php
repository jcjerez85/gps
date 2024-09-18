<?php

namespace Tobuli\Traits;

use App\Exceptions\Manager;
use Tobuli\Entities\User;

trait ModelUserServiceTrait
{
    protected User $user;
    protected Manager $manager;
    protected string $repo;

    public function __construct(User $user, string $repo)
    {
        $this->user = $user;
        $this->manager = new Manager($user);
        $this->repo = $repo;
    }

    public function create(array $data)
    {
        $this->check('create');

        return parent::create($data);
    }

    public function edit($model, array $data)
    {
        $this->check('edit', $model);

        return parent::edit($model, $data);
    }

    public function remove($model)
    {
        $this->check('remove', $model);

        return parent::remove($model);
    }

    protected function check($action, $model = null)
    {
        $this->manager->check($this->repo, $action, $model);
    }
}