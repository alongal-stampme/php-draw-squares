<?php

namespace App;

use App\Geometry\Line;
use App\Geometry\Vertex;
use App\Geometry\LineStream;

class App
{
    public function run()
    {
        $image = 'x6bemq72ac5g1d3p';
        $data = load_json_file($image . '.json');
        $canvas = imagecreatefrompng(__DIR__ . '/../image_files/' . $image . '.jpg');
        $colours = new Colours($canvas);
        imagefill($canvas, 0, 0, $colours->lightGray);

        $document = new JsonDocument($data);
        dd($document->getText());

        $words = $document->getWords();
        $word1 = $words[68];
        $word2 = $words[73];

        $merged1 = $word1->merge($word2);

        $vertex = new Vertex($merged1->vertices);
        $this->draw([$vertex->points], $canvas, $colours->red);
//        dd($vertex->area);

        //        foreach ($words as $index => $word) {
//            if ($index >= 14 && $index <= 16) {
//                $word = $words[$index];
//                $this->draw([$word->vertices], $canvas, $colours->red);
//            }

//            dump($word);
//        }

        // Output and free from memory
        header('Content-Type: image/jpeg');

        imagejpeg($canvas);
        imagedestroy($canvas);
    }

    private function draw(array $line, $canvas, $colour)
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

    private function drawPoint($point, $canvas, $colour)
    {
        imagesetpixel(
            $canvas,
            $point->x,
            $point->y,
            $colour
        );
    }

    private function drawLine($line, $canvas, $colour)
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