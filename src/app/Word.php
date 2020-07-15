<?php

namespace App;

class Word
{
    protected $jsonWord;
    public $text;
    public $vertices;

    public function __construct()
    {
        $this->jsonWord = '';
        $this->text = '';
        $this->vertices = [];
    }

    public function setJson($jsonWord)
    {
        $this->jsonWord = $jsonWord;
        $this->vertices = $jsonWord->boundingBox->vertices;
        $this->extractText();
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function merge(Word $word)
    {
        $outcome = new Word();
        $outcome->text = $this->text . $word->text;
        $outcome->vertices[0] = $this->vertices[0];
        $outcome->vertices[1] = $word->vertices[1];
        $outcome->vertices[2] = $word->vertices[2];
        $outcome->vertices[3] = $this->vertices[3];
        return $outcome;
    }

    private function extractText()
    {
        $text = '';
        foreach ($this->jsonWord->symbols as $symbol) {
            $text .= $symbol->text;
        }
        $this->text = $text;
    }
}