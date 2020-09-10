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

        $this->drawAllWords($document, $canvas);

        /**/
        $collision = $document->words[23]->vertices->collision(
            $document->words[26]->vertices
        );
        $canvas->draw($collision);



//        $line = $document->words[23]->vertices->fullScreenLine();
//        $canvas->draw($document->words[23]->vertices);
//        $canvas->draw($line, $canvas->colours->purple);
//
//        $collision = $line->collisionWithBox($document->words[26]->vertices, $canvas);
//        if ( ! $collision instanceof NullCollision) {
//            $canvas->draw($document->words[26]->vertices);
//        }
//        dd($collision);

        /**/
        // should return the point of the first collision if exists and if doesn't exist then return null

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