<?php

namespace App\Geometry;

class Line
{
    public $points;
    public $centre;
    public $slope;

    public function __construct(array $points = null)
    {
        $this->points = $points;
        $this->centre = $this->calculateCentre();
        $this->slope = $this->calculateSlope();
    }

    private function calculateCentre()
    {
        return (object)[
            'x' => ($this->points[0]->x + $this->points[1]->x) / 2,
            'y' => ($this->points[0]->y + $this->points[1]->y) / 2,
        ];
    }

    private function calculateSlope()
    {
        return ($this->points[1]->y - $this->points[0]->y) / ($this->points[1]->x - $this->points[0]->x);
    }
}