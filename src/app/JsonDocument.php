<?php

namespace App;

use App\NlpTools\GoogleOcrTokenizer;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use App\NlpTools\WhiteSpaceAndColonTokenizer;
use NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;

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
        $space = new GoogleOcrTokenizer();
        $result = $space->tokenize($phrase);
        dd($result);

        $array = [];
        foreach ($this->data->responses[0]->textAnnotations as $annotation) {
            if ($annotation->description == $phrase) {
                $array[] = $annotation->boundingPoly->vertices;
            }
        }
        return $array;
    }
}