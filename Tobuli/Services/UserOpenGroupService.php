<?php

namespace Tobuli\Services;

use DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tobuli\Entities\AbstractGroup;
use Tobuli\Entities\User;

class UserOpenGroupService
{
    private HasMany $query;
    private User $user;
    private string $property;

    public function __construct(HasMany $query)
    {
        $parent = $query->getParent();

        if (get_class($parent) !== User::class) {
            throw new \InvalidArgumentException('Parent must be ' . User::class . ' instance');
        }

        $model = $query->getModel();

        if (!$model instanceof AbstractGroup) {
            throw new \InvalidArgumentException('Unsupported relation model: ' . get_class($model));
        }

        $this->query = $query;
        $this->user = $parent;
        $this->property = $model->keyUngrouped();
    }

    /**
     * @param  int|int[]  $ids
     */
    public function changeStatus($ids, ?bool $status = null): void
    {
        $this->normalizeInput($ids);

        $onlyUngrouped = $this->isOnlyUngrouped($ids);

        if (!$onlyUngrouped) {
            (clone $this->query)->whereIn('id', $ids)->update([
                'open' => is_bool($status) ? $status : DB::raw('!open'),
            ]);
        }

        if (in_array(0, $ids) && (!is_bool($status) || $status != $this->getUngroupedValue())) {
            $this->setUngroupedValue($status);
        }
    }

    private function getUngroupedValue()
    {
        return $this->user->ungrouped_open[$this->property];
    }

    private function setUngroupedValue(?bool $status): void
    {
        $value = $this->user->ungrouped_open;
        $value[$this->property] = (int)(is_bool($status)
            ? $status
            : !($value[$this->property] ?? 0)
        );

        $this->user->update([
            'ungrouped_open' => $value,
        ]);
    }

    private function normalizeInput(&$input)
    {
        if (!is_array($input)) {
            $input = (array)$input;
        }
    }

    private function isOnlyUngrouped(array $input): bool
    {
        return count($input) === 1 && $input[0] == 0;
    }
}