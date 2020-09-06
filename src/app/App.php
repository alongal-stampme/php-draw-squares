<?php

namespace App;

use App\Geometry\FullScreenLine;

class App
{
    // wip
    public function run()
    {
        $image = 'IMG_20200901_102427';
        $document = new JsonDocument(load_json_file($image . '.json'));
        $canvas = new Canvas($image);

        /**/
        for ($i = 0; $i < count($document->words); $i++) {
            $i1 = $i;

            for ($j = 0; $j < count($document->words); $j++) {
                $i2 = $j;

//            if ($index >= $i1 && $index <= $i2) {
                $canvas->draw($document->words[$i1]->vertices);
                $canvas->draw($document->words[$i2]->vertices, $canvas->colours->yellow);

                $line = new FullScreenLine($document->words[$i1]->vertices->median);
                if ($line->slope > -0.1 && $line->slope < 0.1 ) {
                    $canvas->draw($line, $canvas->colours->purple);
                }

//        dd($distance);
            }
        }
        // should return the point of the first collision if exists and if doesn't exist then return null

        $canvas->output();

//        $output = $document->writeToFile($array->toArray(), 'output.txt');
//        echo "<pre>" . $output . "</pre>";
    }
}