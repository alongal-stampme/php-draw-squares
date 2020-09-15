<?php

namespace App;

use App\Geometry\Collision;
use App\Geometry\FullScreenLine;
use App\Geometry\NullCollision;
use Tightenco\Collect\Support\Collection;

class App
{
    public function run()
    {
        $image = 'abcdefg';
        $document = new JsonDocument(load_json_file($image . '.json'));
        $canvas = new Canvas($image);

//        $this->drawAllWords($document, $canvas);


//        foreach ($document->words as $j => $wordJ) {
//            $index = $j;
//        $index = 12;
//        $word0 = $document->words[$index];
//        $word1 = $document->closestWord($word0);
//        $canvas->draw($word0->vertices);
//        $canvas->draw($word1->vertices, $canvas->colours->purple);

//            $canvas->draw($document->words[$index]->vertices, $canvas->colours->purple);
//            foreach ($document->words as $i => $word) {
//                $collision = $document->words[$index]->vertices->collision(
//                    $document->words[$i]->vertices,
//                    $canvas
//                );
//                $canvas->draw($collision);
//                $canvas->draw($collision->distance, $canvas->colours->purple);
//            }
//        }

        // 1. Sort words by Y axis
        $words = $this->sortByYAxis($document->words);
        $words = $this->wordsDerivatives($words);
        $lines = $this->findLinesFromWordsDerivatives($words);
        $lines = $this->sortByXAxis($lines);

        foreach ($lines as $line) {
            dump(collect($line)->pluck('text'));
        }

        $canvas->output();


//        $output = $document->writeToFile($array->toArray(), 'output.txt');
//        echo "<pre>" . $output . "</pre>";
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
            $word->y = $word->vertices->points[0]->y;
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
        $lines = [];
        $line = 0;
        $words = $words->flatten()->all();
        foreach ($words as $index => $word) {
            $diff = $words[$index]->derivative - $words[$index - 1]->derivative;
            if ($diff > 10) $line++;
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