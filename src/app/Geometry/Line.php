<?php

namespace App\Geometry;

class Line
{
    public $points;
    public $centre;
    public $slope;
    public $b;
    public $distance;

    public function __construct(Point $point1 = null, Point $point2 = null)
    {
        $this->points = [$point1, $point2];
        $this->centre = $this->calculateCentre();
        $this->slope = $this->calculateSlope();
        $this->b = $this->calculateB();
        $this->distance = $this->calculateDistance();
    }

    // maybe to delete
    public function collision($shape): Point
    {

    }

    // maybe to delete
    public function collisionWithLine(Line $line): Point
    {
        $s1 = $this->slope;
        $s2 = $line->slope;
        $b1 = $this->b;
        $b2 = $line->b;

        $x = ($b2 - $b1) / ($s1 - $s2);
        $y = ($s1 * $x) + $b1;

        $collisionPoint = new Point($x, $y);

        // We got a collision point but now we need to check if this point
        // is actually on the line. We use the following formula:
        // https://stackoverflow.com/questions/17692922/check-is-a-point-x-y-is-between-two-points-drawn-on-a-straight-line
        /*
            if (distance(a, c) + distance(B, C) == distance(A, B))
                return true; // C is on the line.
            return false;    // C is not on the line.
         */

        $a = $line->points[0];
        $b = $line->points[1];
        $c = $collisionPoint;

        if ($a->distanceFromPoint($c) + $b->distanceFromPoint($c) == $a->distanceFromPoint($b)) {
            return $collisionPoint;
        }

        return new NullPoint();
    }

    // maybe to delete
    public function collisionWithBox(Vertex $vertex, $canvas = null): Point
    {
        $line0 = new Line($vertex->points[0], $vertex->points[1]);
        $line1 = new Line($vertex->points[1], $vertex->points[2]);
        $line2 = new Line($vertex->points[2], $vertex->points[3]);
        $line3 = new Line($vertex->points[3], $vertex->points[0]);

        if ($canvas) {
            $canvas->draw($line0, $canvas->colours->purple);
            $canvas->draw($line1, $canvas->colours->purple);
            $canvas->draw($line2, $canvas->colours->purple);
            $canvas->draw($line3, $canvas->colours->purple);
        }

        $c0 = $this->collisionWithLine($line0);
        $c1 = $this->collisionWithLine($line1);
        $c2 = $this->collisionWithLine($line2);
        $c3 = $this->collisionWithLine($line3);


        if ($canvas) {
            $canvas->draw($c0, $canvas->colours->purple);
            $canvas->draw($c1, $canvas->colours->purple);
            $canvas->draw($c2, $canvas->colours->purple);
            $canvas->draw($c3, $canvas->colours->purple);
        }

        return new Point();
    }

    protected function calculateCentre()
    {
        return new Point(
            ($this->points[0]->x + $this->points[1]->x) / 2,
            ($this->points[0]->y + $this->points[1]->y) / 2
        );
    }

    protected function calculateSlope()
    {
        return ($this->points[1]->y - $this->points[0]->y) / ($this->points[1]->x - $this->points[0]->x);
    }

    protected function calculateB()
    {
        return ($this->centre->y - $this->slope * $this->centre->x);
    }

    private function calculateDistance()
    {
        return $this->points[0]->distanceFromPoint($this->points[1]);
    }
}