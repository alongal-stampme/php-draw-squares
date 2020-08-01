<?php

namespace App;

use App\Geometry\Point;
use App\Geometry\Vertex;
use App\Geometry\VerticesFunctionality;

class App
{
    public function run()
    {
        $image = 'x6bemq72ac5g1d3p';
        $data = load_json_file($image . '.json');

        $document = new JsonDocument($data);
        $wordStreamCount = count($document->text->wordStream);
        $longestWordStreamText = strlen($document->text->sortByCharacterLength('desc')[0]->text);
        $characterXRatio = $document->vertices->width / $longestWordStreamText - 10;
        $characterYRatio = $document->vertices->height / $wordStreamCount - 5;

//        foreach ($document->text->wordStream as $ws) {
//            $x = round($ws->vertices->centre->x / $characterXRatio);
//            $y = round($ws->vertices->centre->y / $characterYRatio);
//            dump("({$x}, {$y}) ==> {$ws->text}");
//        }

        $canvas = new Canvas($image);
        /*
        foreach ($document->words as $word) {
            $canvas->draw($word);
        }
        // */
        /**/
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
        /**/

        // Init multi dimensional array
        $text = [];
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < 100; $j++) {
                $text[$i][$j] = ' ';
            }
        }
        // Copy text into array
        foreach ($array as $line) {
            for ($i = 0; $i < strlen($line['text']); $i++) {
                $y = (int)$line['y'];
                $x = (int)$line['x'];
                $text[$y][$x + $i] = $line['text'][$i];
            }
        }
        // Write to file
        $file = fopen("output.txt", "w+");
        foreach ($text as $line) fwrite($file, implode('', $line) . PHP_EOL);
        fclose($file);


        //*/


//        fwrite($file,$name.PHP_EOL);
//        fwrite($file,$age.PHP_EOL);
//        fwrite($file,$address.PHP_EOL);


//        $canvas->output();
        echo "<pre>" . file_get_contents('output.txt') . "</pre>";
    }
}