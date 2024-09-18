<?php namespace Tobuli\Repositories\EmailTemplate;

use Illuminate\Pagination\LengthAwarePaginator;
use Tobuli\Entities\EmailTemplate as Entity;
use Tobuli\Repositories\EloquentRepository;

class EloquentEmailTemplateRepository extends EloquentRepository implements EmailTemplateRepositoryInterface {

    public function __construct( Entity $entity )
    {
        $this->entity = $entity;
    }

    public function whereName($name)
    {
        return $this->entity->where('name', $name)->first();
    }

    public function searchAndPaginate(array $data, $sort_by, $sort = 'asc', $limit = 10)
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = parent::searchAndPaginate($data, $sort_by, $sort, $limit);

        $sorting = $paginator->sorting;
        $collection = $paginator->getCollection();

        $collection = $collection->filter(function (Entity $emailTemplate) {
            return $emailTemplate->isAvailable();
        });

        $paginator->setCollection($collection);
        $paginator->sorting = $sorting;

        return $paginator;
    }
}