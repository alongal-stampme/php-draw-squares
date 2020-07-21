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

    public function fromPoints(array $points)
    {
        $this->points = $points;
        $this->setup();
        return $this;
    }

    public function moveOnTop(Vertex $anotherVertex)
    {
        $this->points[0]->x = $anotherVertex->points[0]->x;
        $this->points[1]->x = $anotherVertex->points[1]->x + $this->width;
        $this->points[2]->x = $anotherVertex->points[3]->x + $this->width;
        $this->points[3]->x = $anotherVertex->points[3]->x;
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
            'x' => $this->points[0]->x + $this->width / 2,
            'y' => $this->points[0]->y + $this->height / 2,
        ];
    }

    private function calculateArea()
    {
        return $this->width * $this->height;
    }

    private function setup()
    {
        $this->width = $this->calculateWidth();
        $this->height = $this->calculateHeight();
        $this->centre = $this->calculateCentre();
        $this->area = $this->calculateArea();

    }
}