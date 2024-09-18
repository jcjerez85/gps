<?php
namespace Tobuli\Services\FractalSerializers;

use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer;

class WithoutDataArraySerializer extends DataArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        if (empty($resourceKey)) {
            return $data;
        }
        return array($resourceKey => $data);
    }

    public function item($resourceKey, array $data)
    {
        if (empty($resourceKey)) {
            return $data;
        }
        return array($resourceKey => $data);
    }
}