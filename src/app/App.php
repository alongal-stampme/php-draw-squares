<?php

namespace App;

class App
{
    public function run()
    {
        $image = 'IMG_20200711_145840';
        $data = load_json_file($image . '.json');

        $document = new JsonDocument($data);

        $text = $document->text;

        $canvas = new Canvas($image);

        dd($text->wordStream);
        $canvas->draw($text->wordStream);
        $canvas->draw($document);

//        foreach ($words as $i => $word) {
//            if ($i >= 0 && $i <= 0) {
//                $canvas->draw($word->symbols);
//            }
//        }

        $canvas->output();
    }
}