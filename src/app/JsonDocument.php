<?php

namespace App;

use App\Geometry\Collision;
use App\Geometry\FullScreenLine;
use App\Geometry\NullCollision;
use App\Geometry\Vertex;
use Tightenco\Collect\Support\Collection;

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

    public function writeToFile(Collection $lines, $fileName)
    {
        $lineText = '';
        foreach ($lines as $line) {
            $l = '';
            foreach ($line as $word) {
                $l .= $word->text . "\t\t";
            }
            $lineText .= $l . "\n";
        }

        $text = $lineText;
        // Write to file
        $file = fopen("output.txt", "w+");
        fwrite($file,  $text);
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
//            if ($index !== 4) continue;

            $collisionTable = CollisionTable::with($this)->for($word, $canvas);
            $collisionTable = $this->filterOnlyTheClosestCollision($collisionTable);

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

        $forSureSameLine = $this->mergeLinesThatContainsTheSameObjects(collect($forSureSameLine));

        // Sort the new lines by their X axis
        $forSureSameLine = collect($forSureSameLine)->map(function ($line) {
            return collect($line)->sortBy(function ($word) {
                return $word->vertices->centreLeft->x;
            });
        })
            // Sort by the Y axis
            ->sortBy(function ($word) {
                return $word->first()->vertices->centreLeft->y;
            });

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

    private function filterOnlyTheClosestCollision(Collection $collisionTable)
    {
        return $collisionTable->sortByDesc(function ($word) {
            return $word->vertices->centreLeft;
        });
    }

    private function mergeLinesThatContainsTheSameObjects(Collection $forSureSameLine)
    {
        $duplicates = $forSureSameLine->flatten()->values()->duplicates();

        foreach ($duplicates as $duplicate) {
            $list = $this->findInCollection($duplicate, $forSureSameLine);
            $merged = $this->mergeLines($list, $forSureSameLine);
            $forSureSameLine->forget($list->toArray());
            $forSureSameLine->add($merged);
        }
        return $forSureSameLine;
    }

    private function findInCollection($duplicate, Collection $forSureSameLine)
    {
        $lines = collect();
        foreach ($forSureSameLine as $i => $line) {
            foreach ($line as $j => $word) {
                if ($word == $duplicate) {
                    $lines->add($i);
                }
            }
        }
        return $lines;
    }

    private function mergeLines(Collection $list, Collection $forSureSameLine)
    {
        $merged = collect();
        foreach ($list as $key) {
            $merged->add($forSureSameLine->get($key));
        }
        $merged = $merged->flatten()->values()->unique();
        return $merged;
    }
}