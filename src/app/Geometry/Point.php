<?php

namespace App\Geometry;

class Point
{
    protected $json;
    public $x;
    public $y;

    public function __construct($x = 0, $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function fromJson($json)
    {
        $this->json = $json;
        $this->x = $json->x;
        $this->y = $json->y;
        return $this;
    }

    public function distanceFromPoint(Point $point)
    {
        $a = ($this->x - $point->x);
        $b = ($this->y - $point->y);
        return sqrt($a * $a + $b * $b);
    }
}