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
//        $image = 'IMG_20200711_145840';
        $image = 'IMG_20200901_102427';
//        $image = '4pkg2q5hwo81mv6l';
//        $image = 'abcdefg';
        $document = new JsonDocument(load_json_file($image . '.json'));
        $canvas = new Canvas($image);

        $forSureSameLine = [];
        $notSureSameLine = [];
        foreach ($document->text->wordStream as $word) {
            $canvas->draw($word->vertices);
            $collisionTable = CollisionTable::with($document)->for($word);
//            dump($word, $collisionTable);

            if ($collisionTable->isEmpty()) $forSureSameLine[] = [$word->text];
            if ( $collisionTable->first()->isReverseCollision) {
                $forSureSameLine[] = [$word->text, $collisionTable->first()->text];
                foreach ($collisionTable as $w) {
                    $canvas->draw($w->collision);
                }
            }
        }
        dd($forSureSameLine);

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