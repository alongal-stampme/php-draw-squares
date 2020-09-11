<?php

namespace App\Geometry;

class NullCollision extends Collision
{
    public function __construct()
    {
        parent::__construct(null, null, null);
    }
}