<?php

namespace App\Geometry;

class VerticesFunctionality
{
    public function __construct()
    {
    }

    public function willCollide(Vertex $v1, Vertex $v2)
    {
        $new = clone $v2;
        $new->moveOnTop($v1);

        if ($v1->points[1]->x <= $new->points[0]->x && $v1->points[1]->x <= $new->points[3]->x &&
            $v1->points[2]->x <= $new->points[0]->x && $v1->points[2]->x <= $new->points[3]->x) {
            return false;
        }
        if ($v1->points[3]->y <= $new->points[0]->y && $v1->points[3]->y <= $new->points[2]->y &&
            $v1->points[2]->y <= $new->points[0]->y && $v1->points[2]->y <= $new->points[2]->y) {
            return false;
        }
        if ($new->points[0]->y >= $v1->points[3]->y && $new->points[0]->y >= $v1->points[2]->y &&
            $new->points[1]->y >= $v1->points[3]->y && $new->points[1]->y >= $v1->points[2]->y) {
            return false;
        }
        return true;
    }
}