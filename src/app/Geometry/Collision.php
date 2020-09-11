<?php

namespace App\Geometry;

class Collision
{
    public $originVertex;
    public $destinationVertex;
    public $collisionPoints;
    public $fullScreenLine;
    public $distance;

    public function __construct($originVertex, $destinationVertex, $collisionPoints, $fullScreenLine = null)
    {
        $this->originVertex = $originVertex;
        $this->destinationVertex = $destinationVertex;
        $this->collisionPoints = $collisionPoints;
        $this->fullScreenLine = $fullScreenLine;
    }

    public function calculateDistance()
    {
        $shortestLine = new Line();

        foreach ($this->collisionPoints as $point) {
            $line = new Line($this->originVertex->centre, $point);
            if ($line->length < $shortestLine->length) {
                $shortestLine = $line;
            }
        }

        $this->distance = $shortestLine;
        return $this;
    }

}