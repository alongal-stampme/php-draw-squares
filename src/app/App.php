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
        $characterXRatio = $document->vertices->width / $longestWordStreamText;
        $characterYRatio = $document->vertices->height / $wordStreamCount;

//        foreach ($document->text->wordStream as $ws) {
//            $x = round($ws->vertices->centre->x / $characterXRatio);
//            $y = round($ws->vertices->centre->y / $characterYRatio);
//            dump("({$x}, {$y}) ==> {$ws->text}");
//        }

        $canvas = new Canvas($image);

        $array = [];
        $functionality = new VerticesFunctionality();

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
//            dump("({$x}, {$y}) ==> {$ws->text} ---- centre point=({$ws->vertices->centre->x}, {$ws->vertices->centre->y}) x-ratio={$characterXRatio}, y-ratio={$characterYRatio}");
        }

        $canvas->output();
    }
}