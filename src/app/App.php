<?php

namespace App;

class App
{
    public function run()
    {
//        $image = '1599773920-15997738444352654904274009304781';
//        $image = 'IMG_20201219_124815';
//        $image = '19a594d0-04d1-11eb-bd73-a33473376bc3'; // ---------
//        $image = '1b1f2da0-03ee-11eb-a380-fdd0d3f51f92';
        $image = 'IMG_20200907_130804';
//        $image = 'IMG_20200711_145840';
//        $image = 'IMG_20200901_102427';
//        $image = '4pkg2q5hwo81mv6l'; // ---------
//        $image = 'abcdefg';
//        $image = 'example';
//        $image = 'example2'; // ---------
        $document = new JsonDocument(load_json_file($image . '.json'));
        $canvas = null;
//        $canvas = new Canvas($image);

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
            if (isset($word->collisionWithIndex)) $line->push($word->collisionWith);

            // 1.2 ADD LINE TO OVERALL COLLECTION
            $collection->push($line);
        }

        // 2. MERGE LINES WHERE WE HAVE DUPLICATE ITEMS
        $ignoredLines = [];
        foreach ($collection as $index => $line) {
            if (in_array($index, $ignoredLines)) continue;

            $word = $line->get(0);
            // 2.1 If there is no collision for this line in the dataset then we
            // can skip this and move on
            if (! isset($word->collisionWithIndex)) continue;
            if ($word->collisionWithIndex === null) continue;

            // 2.2 Now, when we found a collision, we copy the collided line
            // to the current line
            foreach ($collection->get($word->collisionWithIndex) as $item) {
                $line->push($item);
            }

            // 2.3 Ignore the collided line in our next iterations
            $ignoredLines[] = $word->collisionWithIndex;

            // 2.4 Check collision of the second item in the line
            $word = $line->get(1);

            if (! isset($word->collisionWithIndex)) continue;
            if ($word->collisionWithIndex === null) continue;
            if ($word->collisionWithIndex === $index) continue;

            // 2.5 Merge the collided line of the second word to the line as well
            foreach ($collection->get($word->collisionWithIndex) as $item) {
                $line->push($item);
            }

            $ignoredLines[] = $word->collisionWithIndex;
        }

        // 3. DELETE ALL THE IGNORED LINES
        foreach ($ignoredLines as $ignoredLine) {
            $collection->forget($ignoredLine);
        }

        // 4. REMOVE DUPLICATE LINES
        foreach ($collection as $index => $line) {
            $line = $line->unique();
            $collection[$index] = $line;
        }

        // 5. SORT THE LINES BY THE X AXIS
        foreach ($collection as $index => $line) {
            foreach ($line as $item) {
                $item->x = $item->vertices->centreLeft->x;
                $item->y = $item->vertices->centreLeft->y;
            }
            $line = $line->sortBy('x');
            $collection[$index] = $line;
        }

        // 6. SORT BY Y AXIS
        $collection = $collection->sort(function($a, $b) {
            if ($a[0]->y == $b[0]->y) return 0;
            return ($a[0]->y < $b[0]->y) ? -1 : 1;
        });

        $text = '';
        foreach ($collection as $line) {
            foreach ($line as $word) {
                $text .= $word->text . "\t";
            }
            $text .= "\n";
        }

        dd($text);
//        $lines = $document->organaiseTextInLines();

        $canvas->output();

//        $output = $document->writeToFile($lines, 'output.txt');
//        echo "<pre>" . $output . "</pre>";
    }
}