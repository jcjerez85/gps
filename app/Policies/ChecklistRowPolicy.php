<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Tobuli\Entities\User;

class ChecklistRowPolicy extends Policy
{
    protected $permisionKey = 'checklist_activity';

    public function additionalCheck()
    {
        return config('addon.checklists');
    }

    protected function ownership(User $user, Model $entity)
    {
        return parent::ownership($user, $entity->checklist->service->device);
    }
}
