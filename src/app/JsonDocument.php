<?php

namespace App;

use App\NlpTools\GoogleOcrTokenizer;
use App\NlpTools\CharacterTokenizer;
use Tightenco\Collect\Support\Collection;

class JsonDocument
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function search($phrase, $exact = true)
    {
        $tokens = (new CharacterTokenizer())->tokenize($phrase);
        $symbols = $this->pluckSymbols();

        $indices = $this->isIn($tokens, $symbols, $exact);

        $array = [];
        foreach ($indices as $vertex) {
            $tempArray = [];
            foreach ($symbols as $index => $annotation) {
                if ( ! in_array($index, $vertex)) continue;
                $tempArray[] = $annotation->boundingBox->vertices;
            }
            $array[] = $tempArray;
        }

        return $array;
    }

    public function getWidth()
    {
        return $this->data->responses[0]->fullTextAnnotation->pages[0]->width;
    }

    public function getHeight()
    {
        return $this->data->responses[0]->fullTextAnnotation->pages[0]->height;
    }

    public function getText()
    {
//        return explode(PHP_EOL, $this->data->responses[0]->fullTextAnnotation->text);
        $words = $this->getWords();

        foreach ($words as $word) {
            $text = [];
            $text[] = $word;
            if ($word->breakCharacter == "\n") {
                $array[] = new Text($text);
            }
        }
    }

    public function getBoundingPoly()
    {
        return $this->data->responses[0]->textAnnotations[0]->boundingPoly->vertices;
    }

    public function getVertices()
    {
        $array = [];
        foreach ($this->data->responses[0]->textAnnotations as $annotation) {
            $array[] = $annotation->boundingPoly->vertices;
        }
        return $array;
    }

    public function getVerticesEx()
    {
        $array = [];

        foreach ($this->data->responses[0]->fullTextAnnotation->pages[0]->blocks as $block) {
//            $array[] = $block->boundingBox->vertices;
            foreach ($block->paragraphs as $paragraph) {
//                $array[] = $paragraph->boundingBox->vertices;
                foreach ($paragraph->words as $word) {
//                    $array[] = $word->boundingBox->vertices;
                    foreach ($word->symbols as $symbol) {
                        $array[] = $symbol->boundingBox->vertices;
                    }
                }
            }
        }

        return $array;
    }

    public function getWords()
    {
        $array = [];

        foreach ($this->data->responses[0]->fullTextAnnotation->pages[0]->blocks as $block) {
            foreach ($block->paragraphs as $paragraph) {
                foreach ($paragraph->words as $word) {
                    if ($word->confidence < 0.8) continue;
                    $array[] = new Word($word);
                }
            }
        }

        return $array;
    }

    public function searchEx($phrase)
    {
        $descriptions = $this->pluckDescription($this->data->responses[0]->textAnnotations);

        $space = new GoogleOcrTokenizer();
        $tokens = $space->tokenize($phrase);

        $indices = $this->isIn($tokens, $descriptions);

        $array = [];
        foreach ($indices as $vertex) {
            $tempArray = [];
            foreach ($this->data->responses[0]->textAnnotations as $index => $annotation) {
                if ( ! in_array($index, $vertex)) continue;
                $tempArray[] = $annotation->boundingPoly->vertices;
            }
            $array[] = $tempArray;
        }

        return $array;
    }

    private function isIn($short, $long, $exact)
    {
        $short = collect($short);
        $long = collect(json_decode(json_encode($long), true))->pluck('text');

        $array = [];
        foreach ($long as $i => $itemLong) {
            if ($itemLong === $short[0]) {
                $length = 0;
                $temp = [];
                foreach ($short as $j => $itemShort) {
                    if ($itemShort === $long[$i + $j]) {
                        $length++;
                        $temp[] = $i + $j;
                    }
                }
                if ($length === count($short)) {
                    if ($exact) {
                        if ( ! in_array($long[$i + $j + 1], [
                            '*',
                        ])) $array[] = $temp;
                    } else {
                        $array[] = $temp;
                    }
                }
            }
        }

        return $array;
    }

    private function pluckDescription($textAnnotations)
    {
        $array = [];
        foreach ($textAnnotations as $textAnnotation) {
            $array[] = $textAnnotation->description;
        }
        return $array;
    }

    private function pluckSymbols()
    {
        $array = [];
        foreach ($this->data->responses[0]->fullTextAnnotation->pages[0]->blocks as $block) {
            foreach ($block->paragraphs as $paragraph) {
                foreach ($paragraph->words as $word) {
                    foreach ($word->symbols as $symbol) {
                        $array[] = $symbol;
                        if ($symbol->property->detectedBreak->type == 'SPACE') {
                            $clone = clone $symbol;
                            $clone->text = ' ';
                            $array[] = $clone;
                        }
                    }
                }
            }
        }
//        $array = json_decode(json_encode($array), true);
//        return collect($array);
        return $array;
    }
}