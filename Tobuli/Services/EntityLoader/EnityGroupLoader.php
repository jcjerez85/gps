<?php


namespace Tobuli\Services\EntityLoader;


use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

abstract class EnityGroupLoader extends EnityLoader
{
    protected $queryGroups;

    /**
     * @return LengthAwarePaginator|mixed
     */
    public function get()
    {
        $items = parent::get();

        return $this->groupItems($items);
    }

    public function setQueryGroups($query)
    {
        $this->queryGroups = $query;
    }

    /**
     * @return Builder|null
     */
    public function getQueryGroups()
    {
        return $this->queryGroups ? clone $this->queryGroups : null;
    }

    /**
     * @param LengthAwarePaginator $items
     * @return LengthAwarePaginator
     */
    protected function groupItems(LengthAwarePaginator $items)
    {
        $groupIDs = $items->pluck('group_id')->unique()->all();

        $groups = $this->getQueryGroups()
            ->whereIn('id', $groupIDs)
            ->get()
            ->pluck('title', 'id')
            ->prepend(trans('front.ungrouped'), '0')
            ->all();

        $grouped = [];

        foreach ($items as $item) {
            $group_id = $item->group_id;
            $group_id = (is_null($group_id) || (!Arr::has($groups, $group_id))) ? 0 : $group_id;

            if (empty($grouped[$group_id]))
                $grouped[$group_id] = [
                    'id' => $group_id,
                    'name' => $groups[$group_id],
                    'selected' => true,
                    'items' => [],
                ];

            if (empty($item->selected))
                $grouped[$group_id]['selected'] = false;

            $grouped[$group_id]['items'][] = $item;
        }

        $items->setCollection(collect($grouped));

        return $items;
    }
}