<?php

namespace App;

use App\NlpTools\GoogleOcrTokenizer;

class JsonDocument
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
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
        return explode(PHP_EOL, $this->data->responses[0]->fullTextAnnotation->text);
    }

    public function getVertices()
    {
        $array = [];
        foreach ($this->data->responses[0]->textAnnotations as $annotation) {
            $array[] = $annotation->boundingPoly->vertices;
        }
        return $array;
    }

    public function search($phrase)
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

    private function isIn(array $short, array $long)
    {
        $array = [];
        foreach ($long as $i => $itemLong) {
            if ($itemLong === $short[0]) {
                $length = 0;
                $temp = [];
                foreach ($short as $j => $itemShort) {
                    if ($itemShort === $long[$i + $j]) {
                        $length++;
//                        $temp[] = $long[$i + $j];
                        $temp[] = $i + $j;
                    }
                }
                if ($length === count($short)) {
                    $array[] = $temp;
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
}