<?php

namespace App\Geometry;

class Vertex
{
    protected $json;
    public $points;
    public $width;
    public $height;
    public $centre;
    public $area;

    public function __construct(array $points = null)
    {
        $this->points = $points;
        $this->setup();
    }

    public function fromJson($json)
    {
        $array = [];
        $this->json = $json;
        foreach ($json as $item) {
            $array[] = (new Point())->fromJson($item);
        }
        $this->points = $array;
        $this->setup();
        return $this;
    }

    private function calculateWidth()
    {
        $v = $this->points;
        return abs($v[0]->x - $v[1]->x);
    }

    private function calculateHeight()
    {
        $v = $this->points;
        return abs($v[0]->y - $v[2]->y);
    }


    private function calculateCentre()
    {
        return (object)[
            'x' => $this->points[0]->x + $this->width / 2 + 1,
            'y' => $this->points[0]->y + $this->height / 2 + 1,
        ];
    }

    private function calculateArea()
    {
        $this->area = $this->width * $this->height;
    }

    private function setup()
    {
        $this->width = $this->calculateWidth();
        $this->height = $this->calculateHeight();
        $this->centre = $this->calculateCentre();
        $this->centre = $this->calculateArea();
    }
}