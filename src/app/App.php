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
        for ($i = 0; $i < count($document->words); $i++) {
            if ($i !== 31 && $i !== 31) continue;
            $i1 = $i;

            for ($j = 0; $j < count($document->words); $j++) {
                $i2 = $j;

                $canvas->draw($document->words[$i1]->vertices);

                $line = new FullScreenLine($document->words[$i1]->vertices->median);
                $canvas->draw($line, $canvas->colours->purple);

//        dd($distance);
            }
        }
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