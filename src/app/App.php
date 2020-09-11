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

        $collision = $document->words[23]->vertices->collision(
            $document->words[26]->vertices
        );
        $canvas->draw($collision);

        // TODO create a distance property for the closest point to the center of the origin vertex
        dd($collision->distance);

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