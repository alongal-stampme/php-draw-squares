<?php

namespace App;

class App
{
    public function run()
    {
        $image = 'x6bemq72ac5g1d3p';
        $data = load_json_file($image . '.json');
        $document = new JsonDocument($data);
        $canvas = imagecreatefrompng(__DIR__ . '/../image_files/' . $image . '.jpg');
        $colours = new Colours($canvas);
        imagefill($canvas, 0, 0, $colours->lightGray);

        $totalFor = $document->search('Total for 1 Items');
        $this->draw($totalFor, $canvas, $colours->red);

        $total = $document->search('1.0 36.99');
        $this->draw($total, $canvas, $colours->purple);

        $total = $document->search('36.99*');
        $this->draw($total, $canvas, $colours->yellow);

        $total = $document->search('36.99');
        $this->draw($total, $canvas, $colours->green);

        $this->draw(array_merge($totalFor[0], $total[2]), $canvas, $colours->red);

        // Output and free from memory
        header('Content-Type: image/jpeg');

        imagejpeg($canvas);
        imagedestroy($canvas);
    }

    private function draw(array $line, $canvas, $colour)
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
}