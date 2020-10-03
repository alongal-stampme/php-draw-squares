<?php

namespace App;

use App\Geometry\FullScreenLine;
use Tightenco\Collect\Support\Collection;

class App
{
    public function run()
    {
//        $image = 'IMG_20200711_145840';
//        $image = 'IMG_20200901_102427';
//        $image = '4pkg2q5hwo81mv6l';
//        $image = 'abcdefg';
        $image = 'example';
        $document = new JsonDocument(load_json_file($image . '.json'));
        $canvas = new Canvas($image);

        // 4 == 27/08/2020
        // 8 == 149976
        // 9 == 11:39

        $symbol = collect($document->text->wordStream[4]->words[0]->symbols)->last();
        $line = new FullScreenLine($symbol->vertices->median);
//        $canvas->draw($line, $canvas->colours->purple);
        $canvas->draw($document->text->wordStream[4]->vertices);

        $w = $document->text->wordStream[4];
        $word = $document->text->wordStream[9];
        $collision = $w->getLastSymbol()->vertices->collision(
            $word->getFirstSymbol()->vertices
        );
        $canvas->draw($collision);


        $lines = $document->organaiseTextInLines();

        $lineText = '';
        foreach ($lines as $line) {
            $l = '';
            foreach ($line as $word) {
                $l .= $word->text . "\t\t";
            }
            $lineText .= $l . "\n";
        }
//        dd($lineText);
//        dd('');

//        $canvas->output();
        $output = $document->writeToFile($lines, 'output.txt');
        echo "<pre>" . $output . "</pre>";
    }

    private function drawAllWords(JsonDocument $document, Canvas $canvas)
    {
        for ($i = 0; $i < count($document->words); $i++) {
            $canvas->draw(
                $document->words[$i]->vertices,
                $canvas->colours->yellow
            );
        }
    }

    private function sortByYAxis(array $words)
    {
        return collect($words)->map(function ($word) {
            if ($word->vertices->median->slope >= 0) {
                $word->y = $word->vertices->points[0]->y;
            }
            if ($word->vertices->median->slope < 0) {
                $word->y = $word->vertices->points[1]->y;
            }

            $word->x = $word->vertices->points[0]->x;
            return $word;
        })->sortBy('y');
    }

    private function wordsDerivatives(Collection $words)
    {
        $previous = null;
        return $words->map(function ($word, $index) use ($words, &$previous) {
            if ($words->first() === $word) {
                $previous = $index;
                $word->derivative = 0;
                return $word;
            }
            $d = $words[$index]->y - $words[$previous]->y;
            $previous = $index;
            $word->derivative = $d;
            return $word;
        });
    }

    private function findLinesFromWordsDerivatives(Collection $words)
    {
        $derivativeDiff = 7;
        $lines = [];
        $line = 0;
        $words = $words->flatten()->all();
        foreach ($words as $index => $word) {
            $diff = $words[$index]->derivative - $words[$index - 1]->derivative;

//            dump($word->text, $diff, '-----');

            if ($diff > $derivativeDiff) $line++;
            $lines[$line][] = $word;
        }
        return $lines;
    }

    private function sortByXAxis(array $lines)
    {
        foreach ($lines as $index => $line) {
            $lines[$index] = collect($line)->sortBy('x')
                ->flatten()->all();
        }
        return $lines;
    }
}