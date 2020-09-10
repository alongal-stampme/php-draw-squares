<?php

namespace App\Geometry;

class NullPoint extends Point
{
    public $x;
    public $y;

    public function __construct($x = 0, $y = 0)
    {
        parent::__construct($x, $y);
        $this->x = null;
        $this->y = null;
    }
}