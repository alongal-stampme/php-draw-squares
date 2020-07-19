<?php

namespace App;

use App\Geometry\Vertex;

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
        $this->vertices = new Vertex();
        $this->confidence = 1;
        $this->symbols = [];

        if ($json) $this->setJson($json);
    }

    public function setJson($jsonWord)
    {
        $this->jsonWord = $jsonWord;
        $this->vertices->fromJson($jsonWord->boundingBox->vertices);
        $this->confidence = $jsonWord->confidence;
        $this->setSymbols();
        $this->extractText();
        $this->breakCharacter = end($this->symbols)->breakCharacter();
    }

    public function merge(Word $word)
    {
        if ($this === $word) return $this;

        $outcome = new Word();
        $outcome->text = $this->text . $word->text;

        $outcome->vertices->fromPoints([
            $this->vertices->points[0],
            $word->vertices->points[1],
            $word->vertices->points[2],
            $this->vertices->points[3],
        ]);
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