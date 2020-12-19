<?php

namespace App;

use App\Geometry\FullScreenLine;
use Tightenco\Collect\Support\Collection;

class App
{
    public function run()
    {
//        $image = '1599773920-15997738444352654904274009304781';
        $image = '19a594d0-04d1-11eb-bd73-a33473376bc3';
//        $image = '1b1f2da0-03ee-11eb-a380-fdd0d3f51f92';
//        $image = 'IMG_20200907_130804';
//        $image = 'IMG_20200711_145840';
//        $image = 'IMG_20200901_102427';
//        $image = '4pkg2q5hwo81mv6l';
//        $image = 'abcdefg';
//        $image = 'example';
//        $image = 'example2';
        $document = new JsonDocument(load_json_file($image . '.json'));
        $canvas = new Canvas($image);

//        $symbol = $document->text->wordStream[5]->words[1]->symbols[7];
//        $canvas->draw($symbol->vertices, $canvas->colours->yellow);
//        $line = new FullScreenLine($symbol->vertices->median);
//        $canvas->draw($line, $canvas->colours->purple);
//        $canvas->draw($document->text->wordStream[5]->vertices);

        // 1. GET COLLISIONS
        $collection = collect();
        foreach ($document->text->wordStream as $index => $w) {
            $word = CollisionTable::init($document)
                ->forWord($w)
                ->atIndex($index)
                ->withWords(collect($document->text->wordStream));

            // 1.1. PUT ALL OF THE COLLISIONS IN A TABLE FOR EACH LINE
            $line = collect()->push($word);
            if ($word->collisionWithIndex != null) $line->push($word->collisionWith);

            // 1.2 ADD LINE TO OVERALL COLLECTION
            $collection->push($line);
        }

        // 2. MERGE LINES WHERE WE HAVE DUPLICATE ITEMS
        foreach ($collection as $i => $line) {
            $word = $line->first();
            // 2.1 Search for this word in the entire dataset (collection)
            if ($word->collisionWithIndex === null) continue;
            $line->push()
        }



        // SORT THE LINES BY THE X AXIS
        // REMOVE DUPLICATE LINES
        // [0]
        // [1]
        // ...
        // [10][11]
        // [11][10]
        // [12]
        // [13][15]
        // [14][15]

        dd('---');
//        $w = $document->text->wordStream[15];
//        $canvas->draw($w->vertices, $canvas->colours->yellow);
//
//        $word = CollisionTable::init($document)
//            ->forWord($w)
//            ->withWords(collect($document->text->wordStream));
//
//        dd($word);
//        $canvas->draw($word->collisionWith->vertices);
//        $word = $document->text->wordStream[5];
//        $collision = $w->getLastSymbol()->vertices->collision(
//            $word->getFirstSymbol()->vertices
//        );
//        $canvas->draw($collision);

//        dd($collision->distance->slope);

//        $lines = $document->organaiseTextInLines();

        $canvas->output();

//        $output = $document->writeToFile($lines, 'output.txt');
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