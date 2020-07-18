<?php

namespace App;

class App
{
    public function run()
    {
        $image = '4pkg2q5hwo81mv6l';
        $data = load_json_file($image . '.json');
        $canvas = imagecreatefrompng(__DIR__ . '/../image_files/' . $image . '.jpg');
        $colours = new Colours($canvas);
        imagefill($canvas, 0, 0, $colours->lightGray);

        $document = new JsonDocument($data);

        $words = $document->getWords();
        $text = $document->getText();
//        exit;

//        $text = $document->getText();
//        dump($text);
//        $vertices = $document->search('Total', false);

        foreach ($text as $index => $word) {
            if ($index >= 17 && $index <= 19) {
                $vertices = $document->search($word);
                dump($vertices);
//                $this->draw($vertices, $canvas, $colours->red);
            }
        }

        $line18 = $document->search($text[18]);
        $line19 = $document->search($text[19]);

        $new = $line18;
//        $new[0][] = end($line19[0]);

        $this->draw($new, $canvas, $colours->red);

        // Output and free from memory
        header('Content-Type: image/jpeg');

        imagejpeg($canvas);
        imagedestroy($canvas);
    }

    private
    function draw(array $line, $canvas, $colour)
    {
        if ( ! is_array($line[0][0])) $line = [$line];

        foreach ($line as $vertex) {
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

    private
    function drawPoint($point, $canvas, $colour)
    {
        imagesetpixel(
            $canvas,
            $point->x,
            $point->y,
            $colour
        );
    }

    private
    function drawLine($line, $canvas, $colour)
    {
        imageline(
            $canvas,
            $line[0]->x,
            $line[0]->y,
            $line[1]->x,
            $line[1]->y,
            $colour
        );
    }
}