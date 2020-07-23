<?php

namespace App\Geometry;

class VerticesFunctionality
{
    public function __construct()
    {
    }

    public function willCollide(Vertex $a, Vertex $b)
    {
        $new = $b->moveOnTop($a);
//        return $new;

        if ($a->points[1]->x <= $new->points[0]->x && $a->points[1]->x <= $new->points[3]->x &&
            $a->points[2]->x <= $new->points[0]->x && $a->points[2]->x <= $new->points[3]->x) {
            return false;
        }
        if ($a->points[3]->y <= $new->points[0]->y && $a->points[3]->y <= $new->points[2]->y &&
            $a->points[2]->y <= $new->points[0]->y && $a->points[2]->y <= $new->points[2]->y) {
            return false;
        }
        if ($new->points[0]->y >= $a->points[3]->y && $new->points[0]->y >= $a->points[2]->y &&
            $new->points[1]->y >= $a->points[3]->y && $new->points[1]->y >= $a->points[2]->y) {
            return false;
        }
        if ($new->points[3]->y <= $a->points[1]->y && $new->points[3]->y <= $a->points[0]->y &&
            $new->points[2]->y <= $a->points[1]->y && $new->points[2]->y <= $a->points[0]->y) {
            return false;
        }

        return true;
    }
}