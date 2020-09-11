<?php

namespace App\Geometry;

class Collision
{
    public $originVertex;
    public $destinationVertex;
    public $collisionPoints;
    public $fullScreenLine;

    public function __construct($originVertex, $destinationVertex, $collisionPoints)
    {
        $this->originVertex = $originVertex;
        $this->destinationVertex = $destinationVertex;
        $this->collisionPoints = $collisionPoints;
        $this->fullScreenLine = null;
    }
}