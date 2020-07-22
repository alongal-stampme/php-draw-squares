<?php

namespace App;

use App\Geometry\VerticesFunctionality;

class App
{
    public function run()
    {
        $image = 'jflnevw1igck8mxo';
        $data = load_json_file($image . '.json');

        $document = new JsonDocument($data);
        $text = $document->text;
        $document->sortByYAxis();

//        dd($document->text->wordStream[19]);

        $canvas = new Canvas($image);

        $ws1 = collect($text->wordStream)->get(19);
        $ws2 = collect($text->wordStream)->get(20);
//        $canvas->draw([$ws1, $ws2]);
//        $ws3 = $ws1->merge($ws2);
//        $canvas->draw($ws3);

        $functionality = new VerticesFunctionality();
        if ($functionality->willCollide($ws1->vertices, $ws2->vertices)) {
//            dd($ws1->words);
            $ws3 = $ws1->merge($ws2);
            $canvas->draw($ws3);
        }

//        dd($collision);
//        $canvas->draw($collision, (new Colours($canvas->canvas))->yellow);

//        $canvas->draw($document);

        $canvas->output();
    }
}