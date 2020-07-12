<?php

namespace App;

class App
{
    public function run()
    {
        $image = 'x6bemq72ac5g1d3p';
        $data = load_json_file($image . '.json');
        $document = new JsonDocument($data);

        $canvas = imagecreatetruecolor(
            $document->getWidth(),
            $document->getHeight()
        );
        $canvas = imagecreatefrompng(__DIR__ . '/../image_files/' . $image . '.jpg');

        $colours = new Colours($canvas);
        imagefill($canvas, 0, 0, $colours->lightGray);

        $text = $document->getText();
        foreach ($text as $line) {
            $this->draw(
                $document->search($line),
                $canvas,
                $colours->purple
            );
        }

        // Output and free from memory
        header('Content-Type: image/jpeg');

        imagejpeg($canvas);
        imagedestroy($canvas);
    }

    private function draw(array $vertex, $canvas, $colour)
    {
        imagerectangle(
            $canvas,
            $vertex[0][0]->x,
            $vertex[0][0]->y,
            $vertex[count($vertex) - 1][2]->x,
            $vertex[count($vertex) - 1][2]->y,
            $colour
        );
    }
}