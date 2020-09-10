<?php

namespace App\Geometry;

class Vertex
{
    protected $json;
    public $points;
    public $width;
    public $height;
    public $centre;
    public $centreRight;
    public $centreLeft;
    public $area;
    public $median;

    public function __construct(array $points = null)
    {
        $this->points = $points;
        $this->setup();
    }

    public function fromJson($json)
    {
        $array = [];
        $this->json = $json;
        foreach ($json as $item) {
            $array[] = (new Point())->fromJson($item);
        }
        $this->points = $array;
        $this->setup();
        return $this;
    }

    public function fromPoints(array $points)
    {
        $this->points = $points;
        $this->setup();
        return $this;
    }

    public function moveOnTop(Vertex $anotherVertex)
    {
        $new = new Vertex();
        $new->points[0]->x = $anotherVertex->points[2]->x - 1;
        $new->points[0]->y = $this->points[0]->y;

        $new->points[1]->x = $anotherVertex->points[2]->x + $this->width - 1;
        $new->points[1]->y = $this->points[1]->y;

        $new->points[2]->x = $anotherVertex->points[2]->x + $this->width - 1;
        $new->points[2]->y = $this->points[2]->y;

        $new->points[3]->x = $anotherVertex->points[2]->x - 1;
        $new->points[3]->y = $this->points[3]->y;

        return $new;
    }

    public function distanceFromBox(Vertex $vertex): Line
    {
        return new Line($this->centre, $vertex->centre);
    }

    public function fullScreenLine()
    {
        return new FullScreenLine($this->median);
    }

    private function calculateWidth()
    {
        $v = $this->points;
        return abs($v[0]->x - $v[1]->x);
    }

    private function calculateHeight()
    {
        $v = $this->points;
        return abs($v[0]->y - $v[2]->y);
    }


    private function calculateCentre()
    {
        return new Point(
            $this->points[0]->x + $this->width / 2,
            $this->points[0]->y + $this->height / 2,
        );
    }

    private function calculateCentreRight()
    {
        return new Point(
            ($this->points[1]->x + $this->points[2]->x) / 2,
            ($this->points[1]->y + $this->points[2]->y) / 2
        );
    }

    private function calculateCentreLeft()
    {
        return new Point(
            ($this->points[0]->x + $this->points[3]->x) / 2,
            ($this->points[0]->y + $this->points[3]->y) / 2
        );
    }

    private function calculateArea()
    {
        return $this->width * $this->height;
    }

    private function calculateMedian()
    {
        return new Line($this->centreLeft, $this->centreRight);
    }

    private function setup()
    {
        $this->width = $this->calculateWidth();
        $this->height = $this->calculateHeight();
        $this->centre = $this->calculateCentre();
        $this->centreRight = $this->calculateCentreRight();
        $this->centreLeft = $this->calculateCentreLeft();
        $this->area = $this->calculateArea();
        $this->median = $this->calculateMedian();

    }
}