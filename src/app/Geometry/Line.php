<?php

namespace App\Geometry;

class Line
{
    public $points;
    public $centre;
    public $slope;

    public function __construct(Point $point1 = null, Point $point2 = null)
    {
        $this->points = [$point1, $point2];
        $this->centre = $this->calculateCentre();
        $this->slope = $this->calculateSlope();
    }

    private function calculateCentre()
    {
        return new Point(
            ($this->points[0]->x + $this->points[1]->x) / 2,
            ($this->points[0]->y + $this->points[1]->y) / 2
        );
    }

    private function calculateSlope()
    {
        return ($this->points[1]->y - $this->points[0]->y) / ($this->points[1]->x - $this->points[0]->x);
    }
}