<?php

namespace Tobuli\Services;

use App\Exceptions\Manager;
use Tobuli\Entities\User;
use Tobuli\Entities\Poi;

class PoiUserService extends PoiService
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Manager
     */
    protected $manager;

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
        $this->check('create');

        return parent::create($data);
    }

    public function edit($poi, array $data)
    {
        $this->check('edit', $poi);

        return parent::edit($poi, $data);
    }

    public function remove($poi)
    {
        $this->check('remove', $poi);

        return parent::remove($poi);
    }

    public function active($poi, $active)
    {
        $this->check('active', $poi);

        $this->update($poi, [
            'active' => $active
        ]);
    }

    public function activeMulti($pois, $active)
    {
        $filtered = $pois->filter(function($poi) {
            return $this->user->can('active', $poi);
        });

        if ($filtered->isEmpty())
            return;

        Poi::whereIn('id', $filtered->pluck('id')->all())->update(['active' => $active]);
    }

    protected function check($action, $model = null)
    {
        $this->manager->check('poi', $action, $model);
    }
}
