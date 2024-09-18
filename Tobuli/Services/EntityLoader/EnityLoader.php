<?php


namespace Tobuli\Services\EntityLoader;


use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class EnityLoader
{
    const KEY_SEARCH = 's';
    const LIMIT = 100;

    protected $request_key = null;

    /**
     * @var Builder
     */
    protected $queryItems;

    /**
     * @var Builder
     */
    protected $queryStored;

    abstract protected function transform($item);

    /**
     * @return bool
     */
    public function hasSelect()
    {
        return !empty($this->parseSelectedRequest());
    }

    /**
     * @return LengthAwarePaginator
     */
    public function get()
    {
        $items = $this->getItems();
        $this->applySelected($items);

        $items->appends([
            self::KEY_SEARCH => request()->get(self::KEY_SEARCH)
        ]);

        return $items;
    }

    /**
     * @param string $key
     */
    public function setRequestKey(string $key)
    {
        $this->request_key = 'selected_' . $key;
    }

    /**
     * @return Builder
     */
    public function getAll()
    {
        $where = $this->parseWhere();

        $query = $this->getQueryItems();

        $query = $this->scopeSelect($query, $where[true] ?? null, $this->getQueryStored());
        $query = $this->scopeDeselect($query, $where[false] ?? null);

        return $query;
    }

    public function getSeleted()
    {
        $where = $this->parseWhere();

        if (empty($where[true]))
            return null;

        $query = $this->getQueryItems();
        $query = $this->scopeSelect($query, $where[true] ?? null);

        return $query;
    }

    public function getDeseleted()
    {
        $where = $this->parseWhere();

        if (empty($where[false]))
            return null;

        $query = $this->getQueryItems();
        $query = $this->scopeSelect($query, $where[false] ?? null);

        return $query;
    }

    public function getDetach()
    {
        $where = $this->parseWhere();

        if (empty($where[false]))
            return null;

        $query = $this->getQueryItems();
        $query = $this->scopeSelect($query, $where[false] ?? null);

        if (!empty($where[true]))
            $query = $this->scopeDeselect($query, $where[true] ?? null);

        return $query;
    }

    public function getAttach()
    {
        $where = $this->parseWhere();

        if (empty($where[true]))
            return null;

        $query = $this->getQueryItems();
        $query = $this->scopeSelect($query, $where[true] ?? null);

        return $query;
    }

    protected function scopeDeselect($query, $where, $include = null)
    {
        $query->where(function($q) use ($where, $include){
            if ($include) {
                $sql = $include->select('id')->toRaw();
                $q->orWhereRaw('devices.id NOT IN ('.$sql.')');
            }

            if (!empty($where['id'])) {
                $q->orWhereNotIn('devices.id', $where['id']);
            }

            if (!empty($where['group_id'])) {
                if (in_array(0, $where['group_id']))
                    $q->orWhereNotNull('user_device_pivot.group_id');

                if ($filtered = array_filter($where['group_id'], function($value) {return !empty($value);}))
                    $q->orWhereNotIn('user_device_pivot.group_id', $filtered);
            }

            if (!empty($where['s'])) {
                foreach ($where['s'] as $search) {
                    $q->where(function($query) use ($search){
                        $query->searchExclude($search);
                    });
                }
            }
        });

        return $query;
    }

    protected function scopeSelect($query, $where, $include = null)
    {
        $query->where(function($q) use ($where, $include){
            if ($include) {
                $sql = $include->select('id')->toRaw();
                $q->orWhereRaw('devices.id IN ('.$sql.')');
            }

            if (!empty($where['id'])) {
                $q->orWhereIn('devices.id', $where['id']);
            }

            if (!empty($where['group_id'])) {
                if (in_array(0, $where['group_id']))
                    $q->orWhereNull('user_device_pivot.group_id');

                if ($filtered = array_filter($where['group_id'], function($value) {return !empty($value);}))
                    $q->orWhereIn('user_device_pivot.group_id', $where['group_id']);
            }

            if (!empty($where['s'])) {
                foreach ($where['s'] as $search) {
                    $q->orWhere(function($query) use ($search){
                        $query->search($search);
                    });
                }
            }
        });

        return $query;
    }

    /**
     * @param $query
     */
    public function setQueryItems($query)
    {
        $this->queryItems = $query;
    }

    /**
     * @return Builder|null
     */
    public function getQueryItems()
    {
        return $this->queryItems ? clone $this->queryItems : null;
    }

    /**
     * @param $query
     */
    public function setQueryStored($query)
    {
        $this->queryStored = $query;
    }

    /**
     * @return Builder|null
     */
    public function getQueryStored()
    {
        return $this->queryStored ? clone $this->queryStored : $this->queryStored;
    }

    /**

     * @return LengthAwarePaginator
     */
    protected function getItems()
    {
        if ($id = request()->get('selected_id')) {
            $items = $this->getQueryItems()->whereId($id)->paginate($this->getPageLimit());
        } else {
            $items = $this->getQueryItems()->search(request()->get('s'))->paginate($this->getPageLimit());
        }

        $items->setCollection($items->getCollection()->transform(function ($item) {
            return $this->transform($item);
        }));

        return $items;
    }

    /**
     * @param LengthAwarePaginator $items
     */
    protected function applySelected(LengthAwarePaginator &$items)
    {
        $this->applySelectedStored($items);
        $this->applySelectedRequest($items);
    }

    /**
     * @param LengthAwarePaginator $items
     * @return LengthAwarePaginator|void
     */
    protected function applySelectedStored(LengthAwarePaginator &$items) {
        if(!$this->getQueryStored())
            return;

        $selected = $this->getQueryStored()
            ->whereIn('id', $items->pluck('id')->all())
            ->get()
            ->pluck('id');

        $items->setCollection($items->getCollection()->transform(function ($item) use ($selected){
            if (false !== $selected->search($item->id))
                $item->selected = true;

            return $item;
        }));

        return $items;
    }

    /**
     * @param LengthAwarePaginator $items
     */
    protected function applySelectedRequest(LengthAwarePaginator &$items) {
        $selects = $this->parseSelectedRequest();

        foreach ($selects as $select) {
            list($field, $status, $value) = array_values($select);

            if ($field == 's')
                $filter = function($item) use ($value) {
                    if (empty($value))
                        return true;

                    if (str_contains(strtolower($item->name), strtolower($value)))
                        return true;

                    return false;
                };

            if ($field == 'id')
                $filter = function($item) use ($value) {
                    if ($item->id == $value)
                        return true;

                    return false;
                };

            if ($field == 'group_id')
                $filter = function($item) use ($value) {
                    if ($item->group_id == $value)
                        return true;

                    return false;
                };

            if (!isset($filter))
                return;

            $items->setCollection($items->getCollection()->transform(function ($item) use ($filter, $status) {
                if ($filter($item))
                    $item->selected = $status;

                return $item;
            }));
        }
    }

    /**
     * @return array
     */
    protected function parseSelectedRequest()
    {
        $selected = [];

        foreach (request()->get($this->request_key, []) as $select) {
            list($field, $status, $value) = explode(';', $select, 3);

            $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);

            $selected = array_filter($selected, function($item) use ($field, $status, $value) {
                if($item['field'] == $field && $item['value'] == $value && $item['status'] != $status)
                    return false;

                return true;
            });

            $selected[] = [
                'field' => $field,
                'status' => $status,
                'value' => $value,
            ];
        }

        return $selected;
    }

    protected function parseWhere()
    {
        $selects = $this->parseSelectedRequest();

        $where = [];

        foreach ($selects as $select) {
            list($field, $status, $value) = array_values($select);

            if (empty($where[$status][$field]))
                $where[$status][$field] = [];

            $where[$status][$field][] = $value;
        }

        return $where;
    }

    protected function getPageLimit()
    {
        return config('server.entity_loader_page_limit', self::LIMIT);
    }
}