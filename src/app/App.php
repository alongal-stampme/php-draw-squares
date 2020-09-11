<?php

namespace App;

use App\Geometry\Collision;
use App\Geometry\FullScreenLine;
use App\Geometry\NullCollision;

class App
{
    // wip
    public function run()
    {
        $image = 'IMG_20200907_1308045';
        $document = new JsonDocument(load_json_file($image . '.json'));
        $canvas = new Canvas($image);

//        $this->drawAllWords($document, $canvas);

//        foreach ($document->words as $j => $wordJ) {
//            $index = $j;
        $index = 25;
        $word0 = $document->words[$index];
        $word1 = $document->closestWordToWord($word0);

        $canvas->draw($word0->vertices, $canvas->colours->purple);
        $canvas->draw($word1->vertices, $canvas->colours->purple);
//            $canvas->draw($document->words[$index]->vertices, $canvas->colours->purple);
//            foreach ($document->words as $i => $word) {
//                $collision = $document->words[$index]->vertices->collision(
//                    $document->words[$i]->vertices,
//                    $canvas
//                );
//                $canvas->draw($collision);
//                $canvas->draw($collision->distance, $canvas->colours->purple);
//            }
//        }

        $canvas->output();

//        $output = $document->writeToFile($array->toArray(), 'output.txt');
//        echo "<pre>" . $output . "</pre>";
    }

    private function drawAllWords(JsonDocument $document, Canvas $canvas)
    {
        for ($i = 0; $i < count($document->words); $i++) {
            $canvas->draw(
                $document->words[$i]->vertices,
                $canvas->colours->yellow
            );
        }
    }
}