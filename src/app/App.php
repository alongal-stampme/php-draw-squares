<?php

namespace App;

use App\Geometry\FullScreenLine;

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
        $vertex31 = $document->words[31]->vertices;
        $vertex29 = $document->words[35]->vertices;
        $line31 = $document->words[31]->vertices->fullScreenLine();

        $canvas->draw($vertex31);
        $canvas->draw($vertex29);
        $canvas->draw($line31, $canvas->colours->purple);

        $collision = $line31->collisionWithBox($vertex29, $canvas);

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