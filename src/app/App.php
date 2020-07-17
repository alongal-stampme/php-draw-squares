<?php

namespace App;

use App\Geometry\Line;
use App\Geometry\Vertex;
use App\Geometry\LineStream;

class App
{
    public function run()
    {
        $image = 'abcdefg';
        $data = load_json_file($image . '.json');
        $canvas = imagecreatefrompng(__DIR__ . '/../image_files/' . $image . '.jpg');
        $colours = new Colours($canvas);
        imagefill($canvas, 0, 0, $colours->lightGray);

        $document = new JsonDocument($data);

//        dd($document->getText());
        $words = $document->search("Order #3");

        foreach ($words as $index => $word) {
//            $word = $words[$index];
            $this->draw([$word], $canvas, $colours->red);
        }

        // Output and free from memory
        header('Content-Type: image/jpeg');

        imagejpeg($canvas);
        imagedestroy($canvas);
    }

    private
    function draw(array $line, $canvas, $colour)
    {
        if ( ! is_array($line[0][0])) $line = [$line];

        foreach ($line as $vertex) {
            imagerectangle(
                $canvas,
                $vertex[0][0]->x,
                $vertex[0][0]->y,
                $vertex[count($vertex) - 1][2]->x,
                $vertex[count($vertex) - 1][2]->y,
                $colour
            );
        }
    }

    private
    function drawPoint($point, $canvas, $colour)
    {
        imagesetpixel(
            $canvas,
            $point->x,
            $point->y,
            $colour
        );
    }

    private
    function drawLine($line, $canvas, $colour)
    {
        imageline(
            $canvas,
            $line[0]->x,
            $line[0]->y,
            $line[1]->x,
            $line[1]->y,
            $colour
        );
    }
}