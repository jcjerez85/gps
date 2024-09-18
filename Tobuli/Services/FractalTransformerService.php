<?php

namespace Tobuli\Services;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Pagination\Cursor;
use Tobuli\Services\FractalSerializers\DataArraySerializer;

class FractalTransformerService {

    protected $fractalManager;

    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    protected $data;

    public function __construct(Manager $manager) {
        $this->fractalManager = $manager;
        $this->fractalManager->setSerializer(new DataArraySerializer());

        if (request()->filled('includes')) {
            $this->fractalManager->parseIncludes(request()->get('includes'));
        }
    }

    /**
     * @param SerializerAbstract $transformerClass
     * @return FractalTransformerService
     */
    public function setSerializer($serializerClass) {
        $this->fractalManager->setSerializer(new $serializerClass());
        return $this;
    }


    /**
     * @param TransformerAbstract $transformerClass
     * @return FractalTransformerService
     */
    public function setTransformer($transformerClass) {
        $this->transformer = new $transformerClass();
        return $this;
    }

    /**
     * @param mixed $data
     * @return FractalTransformerService
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set includes for transformer
     *
     * @param array $includes
     * @return FractalTransformerService
     */
    public function setIncludes($includes)
    {
        $this->fractalManager->parseIncludes($includes);

        return $this;
    }

    public function item($data, $transformerClass) {
        $this->setTransformer($transformerClass);
        $this->setData($data);

        $transformedData = new Item($this->data, $this->transformer);
        $transformedData = $this->fractalManager->createData($transformedData);

        return $transformedData;
    }

    public function collection($data, $transformerClass) {
        $this->setTransformer($transformerClass);
        $this->transformer->loadRelations($data);
        $this->setData($data);

        $transformedData = new Collection($this->data, $this->transformer);
        $transformedData = $this->fractalManager->createData($transformedData);
        return $transformedData;
    }

    public function paginate($data, $transformerClass) {
        $this->setTransformer($transformerClass);
        $this->transformer->loadRelations($data);
        $this->setData($data->getCollection());

        $transformedData = new Collection($this->data, $this->transformer);
        $transformedData->setPaginator(new IlluminatePaginatorAdapter($data));
        $transformedData = $this->fractalManager->createData($transformedData);
        return $transformedData;
    }

    /**
     * @param $data
     * @param $transformerClass
     * @return \League\Fractal\Scope
     */
    public function cursorPaginate($data, $transformerClass)
    {
        $this->setTransformer($transformerClass);
        $this->transformer->loadRelations($data);
        $this->setData($data->getCollection());

        $transformedData = new Collection($this->data, $this->transformer);
        $transformedData->setCursor(new Cursor($data, $data->previousCursor(), $data->nextCursor()));
        $transformedData = $this->fractalManager->createData($transformedData);
        return $transformedData;
    }
}