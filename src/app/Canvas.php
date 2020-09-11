<?php

namespace App;

use App\Geometry\Collision;
use App\Geometry\Line;
use App\Geometry\Point;
use App\Geometry\Vertex;
use App\Geometry\FullScreenLine;

class Canvas
{
    protected $image;
    public $canvas;
    public $colours;

    public function __construct($image)
    {
        $this->image = $image;
        $this->canvas = imagecreatefrompng(__DIR__ . '/../image_files/' . $image . '.jpg');
        imagesetthickness($this->canvas, 1);
        $this->colours = new Colours($this->canvas);
    }

    public function draw($shape, $colour = null)
    {
        if (!$colour) $colour = $this->colours->red;

        switch (get_class($shape)) {
            case Point::class:
                $this->drawPoint($shape, $this->canvas, $colour);
                break;
            case Line::class:
            case FullScreenLine::class:
                $this->drawLine($shape, $this->canvas, $colour);
                break;
            case Vertex::class:
                $this->drawVertex($shape, $this->canvas, $colour);
                break;
            case Collision::class:
                $this->drawVertex($shape->originVertex, $this->canvas, $colour);
                $this->drawVertex($shape->destinationVertex, $this->canvas, $colour);
                $this->drawLine($shape->fullScreenLine, $this->canvas, $colour);
                foreach ($shape->collisionPoints as $point) {
                    $this->drawPoint($point, $this->canvas, $colour);
                }
                break;
            default:
                break;
        }

        return $this;
    }

    public function drawPoint(Point $point, $canvas, $colour)
    {
        imagefilledellipse(
            $canvas,
            $point->x,
            $point->y,
            15,
            15,
            $colour
        );
    }

    private function drawLine(Line $line, $canvas, $colour)
    {
        imageline(
            $canvas,
            $line->points[0]->x,
            $line->points[0]->y,
            $line->points[1]->x,
            $line->points[1]->y,
            $colour
        );
    }

    private function drawVertex(Vertex $vertex, $canvas, $colour)
    {
        imagepolygon(
            $canvas,
            [
                $vertex->points[0]->x, $vertex->points[0]->y,
                $vertex->points[1]->x, $vertex->points[1]->y,
                $vertex->points[2]->x, $vertex->points[2]->y,
                $vertex->points[3]->x, $vertex->points[3]->y,
            ],
            4,
            $colour
        );
    }

    private function drawShapes($shapes, $canvas, $colour)
    {
        if (!is_array($shapes)) $shapes = [$shapes];
        foreach ($shapes as $shape) {
            if (isset($shape->vertices)) {
                $this->draw($shape->vertices, $colour);
                continue;
            }
            $this->draw($shape, $colour);
        }
    }

    public function output()
    {
        header('Content-Type: image/jpeg');
        imagejpeg($this->canvas);
        imagedestroy($this->canvas);
    }
}