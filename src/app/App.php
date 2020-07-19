<?php

namespace App;

use App\Geometry\Line;
use App\Geometry\Point;
use App\Geometry\Vertex;

class App
{
    public function run()
    {
        $image = '4pkg2q5hwo81mv6l';
        $data = load_json_file($image . '.json');

        $document = new JsonDocument($data);

        $canvas = new Canvas($image);
        foreach ($document->data->responses[0]->textAnnotations as $textAnnotation) {
            $canvas->draw((new Vertex())->fromJson($textAnnotation->boundingPoly->vertices));
        }

        $canvas->output();
    }
}