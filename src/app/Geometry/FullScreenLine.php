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
        $this->points[1]->x = 5500;
        $this->points[1]->y = $this->slope * 5500 + $this->b;
    }

    public function collisionWithBox(Vertex $vertex, $canvas = null): Collision
    {
        $line0 = new Line($vertex->points[0], $vertex->points[1]);
        $line1 = new Line($vertex->points[1], $vertex->points[2]);
        $line2 = new Line($vertex->points[2], $vertex->points[3]);
        $line3 = new Line($vertex->points[3], $vertex->points[0]);

        if ($canvas) {
//            $canvas->draw($line0, $canvas->colours->purple);
//            $canvas->draw($line1, $canvas->colours->purple);
//            $canvas->draw($line2, $canvas->colours->purple);
            $canvas->draw($line3, $canvas->colours->purple);
        }

        $c0 = $this->collisionWithLine($line0, $canvas);
        $c1 = $this->collisionWithLine($line1, $canvas);
        $c2 = $this->collisionWithLine($line2, $canvas);
        $c3 = $this->collisionWithLine($line3, $canvas);

        $collisionPoints = [];
        if (!$c0 instanceof NullPoint) $collisionPoints[] = $c0;
        if (!$c1 instanceof NullPoint) $collisionPoints[] = $c1;
        if (!$c2 instanceof NullPoint) $collisionPoints[] = $c2;
        if (!$c3 instanceof NullPoint) $collisionPoints[] = $c3;
        $collisionPoints = collect($collisionPoints);

        if ($canvas) {
            $canvas->draw($c0, $canvas->colours->purple);
            $canvas->draw($c1, $canvas->colours->purple);
            $canvas->draw($c2, $canvas->colours->purple);
            $canvas->draw($c3, $canvas->colours->purple);
        }

        if ($collisionPoints->isEmpty()) return new NullCollision();

        return new Collision(
            $this,
            $vertex,
            $collisionPoints
        );
    }
}