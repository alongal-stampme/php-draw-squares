<?php

namespace App;

class App
{
    public function run()
    {
        $image = '4pkg2q5hwo81mv6l';
        $data = load_json_file($image . '.json');
        $document = new JsonDocument($data);

        $canvas = imagecreatetruecolor(
            $document->getWidth(),
            $document->getHeight()
        );
        $canvas = imagecreatefrompng(__DIR__ . '/../image_files/' . $image . '.jpg');

        $colours = new Colours($canvas);
        imagefill($canvas, 0, 0, $colours->lightGray);

        $list = $document->search('TOTAL: 25.29');
//        $list = $document->getVertices();

        foreach ($list as $vertex) {
            imagerectangle(
                $canvas,
                $vertex[0]->x,
                $vertex[0]->y,
                $vertex[2]->x,
                $vertex[2]->y,
                $colours->red
            );
        }

        // Output and free from memory
        header('Content-Type: image/jpeg');

        imagejpeg($canvas);
        imagedestroy($canvas);
    }
}