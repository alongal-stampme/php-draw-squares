<?php

namespace App;

use App\Geometry\Collision;
use App\Geometry\NullCollision;
use App\Geometry\Vertex;

class JsonDocument
{
    public $json;
    public $width;
    public $height;
    public $words;
    public $text;
    public $vertices;

    public function __construct($json)
    {
        $this->json = $json;
        $this->width = $this->json->responses[0]->fullTextAnnotation->pages[0]->width;
        $this->height = $this->json->responses[0]->fullTextAnnotation->pages[0]->height;
        $this->words = $this->generateWords();
        $this->text = $this->generateText();
        $this->vertices = (new Vertex())->fromJson($this->json->responses[0]->textAnnotations[0]->boundingPoly->vertices);
    }

    public function sortByYAxis()
    {
        $result = collect($this->text->wordStream)->sortBy(function ($wordStream) {
            return $wordStream->vertices->centre->y;
        });
        $this->text->wordStream = $result->toArray();
    }

    public function sortByXAxis()
    {
        $result = collect($this->text->wordStream)->sortBy(function ($wordStream) {
            return $wordStream->vertices->centre->x;
        });
        $this->text->wordStream = $result->toArray();
    }

    public function characterCount()
    {
        return $this->text->characterCount();
    }

    public function writeToFile(array $data, $fileName)
    {
        // Init multi dimensional array
        $text = [];
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < 100; $j++) {
                $text[$i][$j] = ' ';
            }
        }

        // Copy text into array
        foreach ($data as $line) {
            for ($i = 0; $i < strlen($line['text']); $i++) {
                $y = (int)$line['y'];
                $x = (int)$line['x'];
                $text[$y][$x + $i] = $line['text'][$i];
            }
        }
        // Write to file
        $file = fopen("output.txt", "w+");
        foreach ($text as $line) fwrite($file, implode('', $line) . PHP_EOL);
        fclose($file);

        return file_get_contents($fileName);
    }

    public function closestWord(Word $word)
    {
        $distances = collect($this->words)
            ->map(function ($w) use ($word) {
                if ($w === $word) return;

                $collision = $word->vertices->collision($w->vertices);

                if (!$collision instanceof NullCollision) {
                    $collision->length = $collision->distance->length;
                    return $collision;
                }
            })
            ->filter(function ($collision) {
                return !is_null($collision);
            })
            ->sortBy('distance.length');

//        dd($distances);
        return $this->words[$distances->keys()->first()];
    }

    public function organaiseTextInLines($canvas = null)
    {
        $forSureSameLine = [];
        $notSureSameLine = [];
        foreach ($this->text->wordStream as $index => $word) {
            $collisionTable = collect();
//            if ($index == 12) {
            $collisionTable = CollisionTable::with($this)->for($word, $canvas);
//            }

            // If there is no collision to the word then for
            // sure it is the same line as itself
            if ($collisionTable->isEmpty()) {
                $forSureSameLine[] = [$word];
                continue;
            }

            // If the word has a reverse collision (ie: the word that
            // it collide with also collide back, then it is
            // for sure the same line
            if ($collisionTable->first()->isReverseCollision) {
                if ($word->vertices->centreLeft >= $collisionTable->first()->vertices->centreLeft) continue;

                $forSureSameLine[] = [$word, $collisionTable->first()];
                continue;
            }

            // For everything else, we put in a separate array
            // for further investigation
            $word->collisionWith = $collisionTable->first();
            $notSureSameLine[] = [$word];
        }

        // Filter out all the words that their collision appears
        // for sure in a separate line somewhere else
        $sameLine = collect($notSureSameLine)
            ->filter(function ($notSure, $key) {
                if ($notSure[0]->collisionWith->isReverseCollision) return false;
                return true;
            })
            ->values();

        // Double check all the words that we not sure about
        foreach ($sameLine as $word) {
            $isCollision = CollisionTable::with($this)->doubleCheck($word[0], $word[0]->collisionWith, $canvas);
            if (!$isCollision) {
                $forSureSameLine[] = [$word[0]];
                $forSureSameLine[] = [$word[0]->collisionWith];
                continue;
            }
            $forSureSameLine[] = [$word[0], $word[0]->collisionWith];
        }

        // Sort the new lines by their X axis
        $forSureSameLine = collect($forSureSameLine)->map(function ($line) {
            return collect($line)->sortBy(function ($word) {
                return $word->vertices->centreLeft->x;
            });
        })
            ->sortBy(function ($word) {
                return $word->first()->vertices->centreLeft->y;
            });

        // Sort by the Y axis

        return $forSureSameLine;
    }

    private function generateWords()
    {
        $array = [];

        foreach ($this->json->responses[0]->fullTextAnnotation->pages[0]->blocks as $block) {
            foreach ($block->paragraphs as $paragraph) {
                foreach ($paragraph->words as $word) {
//                    if ($word->confidence < 0.8) continue;
                    $array[] = new Word($word);
                }
            }
        }

        return $array;
    }

    private function generateText()
    {
        return (new Text)->fromWords($this->words);
    }
}