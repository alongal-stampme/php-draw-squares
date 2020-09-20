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
//        $image = 'IMG_20200901_102427';
        $image = 'abcdefg';
        $document = new JsonDocument(load_json_file($image . '.json'));
        $canvas = new Canvas($image);

        // 2, 4, 5
        $word2 = $document->text->wordStream[2];
        $word4 = $document->text->wordStream[4];
        $word5 = $document->text->wordStream[5];

        $canvas->draw($word2->vertices);
        $canvas->draw($word4->vertices);
        $canvas->draw($word5->vertices);

        $word2LastSymbol = collect(collect($word2->words)->last()->symbols)->last();
        $word4FirstSymbol = collect(collect($word4->words)->first()->symbols)->first();

        $canvas->draw($word2LastSymbol->vertices, $canvas->colours->green);
        $canvas->draw($word4FirstSymbol->vertices, $canvas->colours->green);

        $line2 = new FullScreenLine($word2LastSymbol->vertices->median);
        $line4 = new FullScreenLine($word4FirstSymbol->vertices->median);

        $canvas->draw($line2, $canvas->colours->purple);
        $canvas->draw($line4, $canvas->colours->purple);

        $collisionTable = CollisionTable::makeFor($word2);

//        $collision = $line->collisionWithBox($word4FirstSymbol->vertices);
//        dd($collision);
//        $canvas->draw($collision, $canvas->colours->purple);
//        dd(collect($word2->words)->last());

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