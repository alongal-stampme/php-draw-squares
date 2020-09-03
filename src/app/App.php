<?php

namespace App;

use App\Geometry\Line;
use App\Geometry\Point;
use App\Geometry\Vertex;
use App\Geometry\FullScreenLine;
use App\Geometry\VerticesFunctionality;

class App
{
    // wip
    public function run()
    {
        $image = 'IMG_20200901_102427';
        $data = load_json_file($image . '.json');

        $document = new JsonDocument($data);
        $wordStreamCount = count($document->text->wordStream);
        $longestWordStreamText = strlen($document->text->sortByCharacterLength('desc')[0]->text);
        $characterXRatio = $document->vertices->width / $longestWordStreamText - 10;
        $characterYRatio = $document->vertices->height / $wordStreamCount - 5;

        $canvas = new Canvas($image);
        /**/
//        foreach ($document->words as $index => $word) {
//            if ($index >= 12 && $index <= 13) {
//                $canvas->draw($word->vertices);
//            }

        $line1 = new FullScreenLine($document->words[12]->vertices->median);
        $line2 = new FullScreenLine($document->words[13]->vertices->median);
        $canvas->draw($line1);
        $canvas->draw($line2, $canvas->colours->yellow);

        $collisionPoint = $line1->collision($line2);    // should be allowed to check collision with any shape
        // should return the point of the first collision if exists and if doesn't exist then return null

//        dump($document->words[49]->vertices->median);
//        dump($document->words[50]->vertices->median);
//        }
        // */
        /*
        $array = [];
        foreach ($document->text->wordStream as $index => $ws) {
//            if ($index < 18 || $index > 20) continue;

            $v = new Vertex([
                new Point(0, $ws->vertices->points[0]->y),
                new Point($document->width, $ws->vertices->points[1]->y),
                new Point($document->width, $ws->vertices->points[2]->y),
                new Point(0, $ws->vertices->points[3]->y),
            ]);

            $canvas->draw($ws->vertices->centre, $canvas->colours->purple);
            $canvas->draw($ws);
//            $canvas->draw($v);
            $canvas->draw($v->centre, $canvas->colours->yellow);

            $x = $ws->vertices->centre->x / $characterXRatio;
            $y = $ws->vertices->centre->y / $characterYRatio;
            $array[] = [
                'x' => $x,
                'y' => $y,
                'text' => $ws->text
            ];
        }
        $array = collect($array)->sortBy('y')->values();
        //*/
        $canvas->output();

//        $output = $document->writeToFile($array->toArray(), 'output.txt');
//        echo "<pre>" . $output . "</pre>";
    }
}