<?php namespace Tobuli\Repositories\ReportLog;

use Tobuli\Entities\ReportLog as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentReportLogRepository extends EloquentRepository implements ReportLogRepositoryInterface {

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
            ->orderBy($sort['sort_by'], $sort['sort']);

        if (array_key_exists('user_ids', $data['filter'])) {
            $items = $items->whereIn('user_id', $data['filter']['user_ids']);
            unset($data['filter']['user_ids']);
        }

        $items = $items->where(function ($query) use ($data) {
                if (method_exists($this->entity,'scopeSearch')) {
                    $query->search($data['search_phrase'] ?? null);
                }

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