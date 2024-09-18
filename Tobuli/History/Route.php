<?php

namespace Tobuli\History;

use Illuminate\Support\Arr;

class Route
{
    protected $routes = [];

    protected $color;
    protected $point;
    protected $reference;

    public function apply($position)
    {
        if ($this->color != $position->color) {
            if ($this->reference)
                $this->add($position);

            array_push($this->routes, [
                'color' => $position->color,
                'items' => []
            ]);

            end($this->routes);
            $this->reference = & $this->routes[key($this->routes)];

            $this->point = null;
            $this->color = $position->color;
        }

        //$point = "{$position->latitude} {$position->longitude}";
        $point = round($position->latitude, 5) .":". round($position->longitude, 5);

        if ($this->point == $point)
            return;

        $this->point = $point;

        $this->add($position);
    }

    public function getPolylines()
    {
        $polylines = [];

        foreach ($this->routes as $route){
            $polylines[] = [
                'color'   => $route['color'],
                'latlngs' => Arr::pluck($route['items'], 'p')
            ];
        }

        return $polylines;
    }

    protected function add($position)
    {
        $this->reference['items'][] = [
            'i' => $position->id,
            'c' => $position->course,
            'p' => [
                $position->latitude,
                $position->longitude
            ]
        ];
    }

    public function __destruct()
    {
        $this->color = null;
        $this->point = null;
        $this->routes = null;
    }
}