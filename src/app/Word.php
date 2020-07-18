<?php

namespace App;

class Word
{
    protected $jsonWord;
    public $text;
    public $breakCharacter;
    public $vertices;
    public $confidence;
    public $symbols;

    public function __construct($json = null)
    {
        $this->jsonWord = '';
        $this->text = '';
        $this->breakCharacter = null;
        $this->vertices = [];
        $this->confidence = 0.0;
        $this->symbols = [];

        if ($json) $this->setJson($json);
    }

    public function setJson($jsonWord)
    {
        $this->jsonWord = $jsonWord;
        $this->vertices = $jsonWord->boundingBox->vertices;
        $this->confidence = $jsonWord->confidence;
        $this->setSymbols();
        $this->extractText();
        $this->breakCharacter = end($this->symbols)->breakCharacter();
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
        foreach ($this->symbols as $symbol) {
            $text .= $symbol->text;
        }
        $this->text = $text;
    }

    private function setSymbols()
    {
        $array = [];
        foreach ($this->jsonWord->symbols as $symbol) {
            $array[] = new Symbol($symbol);
        }
        $this->symbols = $array;
    }
}