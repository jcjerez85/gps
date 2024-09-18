<?php namespace Tobuli\Repositories\Tasks;

use Tobuli\Entities\Task as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentTasksRepository extends EloquentRepository implements TasksRepositoryInterface {

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

        $query = $this->entity
            ->orderBy($sort['sort_by'], $sort['sort'])
            ->where(function ($query) use ($data) {
                if (count($data['filter'])) {
                    foreach ($data['filter'] as $key => $value) {
                        switch ($key) {
                            case 'accessible_user_id':
                                break;
                            case 'pickup_time_from':
                            case 'delivery_time_from':
                                $query->where("tasks.$key", '>=', $value);
                                break;
                            case 'pickup_time_to':
                            case 'delivery_time_to':
                                $query->where("tasks.$key", '<=', $value);
                                break;
                            default:
                                $query->where("tasks.$key", $value);
                                break;
                        }

                    }
                }
            });

        if (!empty($data['filter']['accessible_user_id'])) {
            $query
                ->select("{$this->entity->getTable()}.*")
                ->accessibleUser($data['filter']['accessible_user_id']);
        }

        $items = $query->paginate($limit);

        $items->sorting = $sort;

        return $items;
    }

    public function findWithAttributes($id) {
        return Entity::where('id', $id)->with('statuses')->first();
    }
}