<?php

namespace Tobuli\Importers\Route;

use CustomFacades\Repositories\RouteRepo;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Importers\Importer;

class RouteImporter extends Importer
{
    protected $defaults = [
        'active' => true,
        'color'  => '#ffffff',
    ];

    protected function getDefaults()
    {
        return $this->defaults;
    }

    protected function importItem($data, $attributes = [])
    {
        $data = $this->mergeDefaults($data);
        $data = $this->setUser($data, $attributes);

        if ( ! $this->validate($data)) {
            return;
        }

        $this->normalize($data);

        if ($this->getRoute($data)) {
            return;
        }

        $this->create($data);
    }

    private function normalize(array &$data): array
    {
        return $data;
    }

    private function getRoute($data)
    {
        return RouteRepo::first($data);
    }

    private function create($data)
    {
        beginTransaction();
        try {
            RouteRepo::create($data);
        } catch (\Exception $e) {
            rollbackTransaction();
            throw $e;
        }
        commitTransaction();
    }

    public function getValidationBaseRules(): array
    {
        return [
            'name'          => 'required',
            'polyline'      => 'required',
            'color'         => 'required|min:7|max:7',
        ];
    }
}
