<?php

namespace App\Geometry;

class FullScreenLine extends Line
{
    public $line;

    public function __construct(Line $line)
    {
        parent::__construct($line->points[0], $line->points[1]);
        $this->line = $line;
        $this->arrangePoints();
    }

    protected function arrangePoints()
    {
        $this->points[0]->x = 0;
        $this->points[0]->y = $this->b;
        $this->points[1]->x = 1500;
        $this->points[1]->y = $this->slope * 1500 + $this->b;
    }
}