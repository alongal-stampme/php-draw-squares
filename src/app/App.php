<?php

namespace App;

use App\Geometry\Point;
use App\Geometry\Vertex;
use App\Geometry\VerticesFunctionality;

class App
{
    public function run()
    {
        $image = 'oscars-mexican-food';
        $data = load_json_file($image . '.json');

        $document = new JsonDocument($data);
        $text = $document->text;
//        dd(count($document->text->wordStream));
//        $document->sortByYAxis();


        $canvas = new Canvas($image);
//        $v = new Vertex([
//            new Point(722, 235),
//            new Point(955, 208),
//            new Point(955, 954),
//            new Point(722,981)
//        ]);
//        $canvas->draw($v);

        $array = [];
        $functionality = new VerticesFunctionality();
        foreach ($text->wordStream as $i => $wsi) {
//            foreach ($text->wordStream as $j => $wsj) {
            $canvas->draw($document);
            dump($text->wordStream[$i]);
//                $test = $functionality->willCollide($text->wordStream[$i]->vertices, $text->wordStream[$j]->vertices);
//                if ($test && ($i != $j) &&
//                    $text->wordStream[$i]->vertices->points[0]->x < $text->wordStream[$j]->vertices->points[0]->x)
//                    $ws = $text->wordStream[$i]->merge($text->wordStream[$j]);
//                    $array[] = $ws;
//                    dump($ws->text);
//                }
//            }
        }
//
//        foreach ($array as $ws) {
//            $canvas->draw($ws);
//        }


//        dd($collision);
//        $canvas->draw($collision, (new Colours($canvas->canvas))->yellow);

//        $canvas->draw($document);

        $canvas->output();
    }
}