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
}