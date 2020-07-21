<?php

namespace App;

class App
{
    public function run()
    {
        $image = 'jflnevw1igck8mxo';
        $data = load_json_file($image . '.json');

        $document = new JsonDocument($data);
        $text = $document->text;
        $document->sortByYAxis();

        dd(collect($text->wordStream)->first());

        $canvas = new Canvas($image);
        $canvas->draw(collect($text->wordStream)->first());
//        $canvas->draw($document);

        $canvas->output();
    }
}