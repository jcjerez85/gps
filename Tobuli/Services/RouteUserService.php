<?php

namespace Tobuli\Services;

use App\Exceptions\Manager;
use Tobuli\Entities\Route;
use Tobuli\Entities\User;

class RouteUserService extends RouteService
{
    protected User $user;
    protected Manager $manager;

    public function __construct(User $user)
    {
        parent::__construct();

        $this->user = $user;
        $this->manager = new Manager($user);
    }

    protected function normalize(array $data)
    {
        return parent::normalize($data) + ['user_id' => $this->user->id];
    }

    public function create(array $data)
    {
//        echo '<pre>'; var_dump($data);exit;
        $this->check('create');

        return parent::create($data);
    }

    public function edit($route, array $data)
    {
        $this->check('edit', $route);

        return parent::edit($route, $data);
    }

    public function remove($route)
    {
        $this->check('remove', $route);

        return parent::remove($route);
    }

    public function active($route, $active)
    {
        $this->check('active', $route);

        $this->update($route, [
            'active' => $active
        ]);
    }

    public function activeMulti($routes, $active)
    {
        $filtered = $routes->filter(function($route) {
            return $this->user->can('active', $route);
        });

        if ($filtered->isEmpty())
            return;

        Route::whereIn('id', $filtered->pluck('id')->all())->update(['active' => $active]);
    }

    protected function check($action, $model = null)
    {
        $this->manager->check('routes', $action, $model);
    }
}
