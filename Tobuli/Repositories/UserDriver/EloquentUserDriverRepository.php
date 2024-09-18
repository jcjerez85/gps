<?php namespace Tobuli\Repositories\UserDriver;

use Tobuli\Entities\UserDriver as Entity;
use Tobuli\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Auth;

class EloquentUserDriverRepository extends EloquentRepository implements UserDriverRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function searchAndPaginate(array $data, $sort_by, $sort = 'asc', $limit = 10)
    {
        $data = $this->generateSearchData($data);
        $sort = array_merge([
            'sort' => $sort,
            'sort_by' => $sort_by
        ], $data['sorting']);

        $items = $this->entity
            ->orderBy($sort['sort_by'], $sort['sort'])
            ->with('device')
            ->search($data['search_phrase'] ?? null)
            ->where(function ($query) use ($data) {
                if (count($data['filter'])) {
                    foreach ($data['filter'] as $key=>$value) {
                        $query->where($key, $value);
                    }
                }
            })
            ->paginate($limit);

        $items->sorting = $sort;

        return $items;
    }


}