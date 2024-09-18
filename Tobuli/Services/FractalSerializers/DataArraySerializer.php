<?php
namespace Tobuli\Services\FractalSerializers;

use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer;

class DataArraySerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey === false) {
            return $data;
        }
        return array($resourceKey ?: 'data' => $data);
    }

    public function item($resourceKey, array $data)
    {
        if ($resourceKey === false) {
            return $data;
        }
        return array($resourceKey ?: 'data' => $data);
    }


    /**
     * Serialize the meta.
     *
     * @param array $meta
     *
     * @return array
     */
    public function meta(array $meta)
    {
        if (empty($meta)) {
            return [];
        }

        return $meta;
    }

    /**
     * Serialize the paginator.
     *
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function paginator(PaginatorInterface $paginator)
    {
        $currentPage = (int) $paginator->getCurrentPage();
        $lastPage = (int) $paginator->getLastPage();
        $perPage = (int) $paginator->getPerPage();

        $pagination = [
            'total'         => (int) $paginator->getTotal(),
            'per_page'      => $perPage,
            'current_page'  => $currentPage,
            'last_page'     => $lastPage,
            'next_page_url' => $currentPage < $lastPage ? $paginator->getUrl($currentPage + 1) : null,
            'prev_page_url' => $currentPage > 1 ? $paginator->getUrl($currentPage - 1) : null,
        ];

        return ['pagination' => $pagination];
    }

    /**
     * @param CursorInterface $cursor
     * @return array|array[]
     */
    public function cursor(CursorInterface $cursor)
    {
        $current = $cursor->getCurrent();
        return [
            'pagination' => [
                'per_page' => $current->perPage(),
                'next_page_url' => $current->nextPageUrl(),
                'prev_page_url' => $current->previousPageUrl(),
            ]
        ];
    }
}