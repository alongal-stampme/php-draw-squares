<?php

namespace App;

use App\Geometry\Line;
use App\Geometry\Point;
use App\Geometry\Vertex;

class Canvas
{
    protected $image;
    protected $canvas;
    public $colours;

    public function __construct($image)
    {
        $this->image = $image;
        $this->canvas = imagecreatefrompng(__DIR__ . '/../image_files/' . $image . '.jpg');
        imagesetthickness($this->canvas, 3);
        $this->colours = new Colours($this->canvas);
    }

    public function draw($shape, $colour = null)
    {
        if ( ! $colour) $colour = $this->colours->red;

        switch (get_class($shape)) {
            case Point::class:
                $this->drawPoint($shape, $this->canvas, $colour);
                break;
            case Line::class:
                $this->drawLine($shape, $this->canvas, $colour);
                break;
            case Vertex::class:
                $this->drawVertex($shape, $this->canvas, $colour);
                break;
            default:
                $this->drawShapes($shape, $this->canvas, $colour);
                break;
        }

        return $this;
    }

    private function drawPoint(Point $point, $canvas, $colour)
    {
        imagesetpixel($canvas, $point->x, $point->y, $colour);
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
        imagerectangle(
            $canvas,
            $vertex->points[0]->x,
            $vertex->points[0]->y,
            $vertex->points[count($vertex->points) - 2]->x,
            $vertex->points[count($vertex->points) - 2]->y,
            $colour
        );
    }

    private function drawShapes($shapes, $canvas, $colour)
    {
        if ( ! is_array($shapes)) $shapes = [$shapes];
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