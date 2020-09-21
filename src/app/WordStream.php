<?php

namespace App;

use App\Geometry\Point;
use App\Geometry\Vertex;

class WordStream
{
    public $words = [];
    public $vertices;
    public $text;

    public function __construct(array $words)
    {
        $this->words = $words;
        $this->vertices = $this->calculateVertices();
        $this->text = $this->extractText();
    }

    public function merge(WordStream $wordStream)
    {
        if ($this === $wordStream) return $this;

        $new = array_merge($this->words, $wordStream->words);
        $outcome = new WordStream($new);
        $outcome->text = $this->text . ' ' . $wordStream->text;

        return $outcome;
    }

    public function characterCount()
    {
        return strlen($this->text);
    }

    public function getLastSymbol()
    {
        return collect(collect($this->words)->last()->symbols)->last();
    }

    public function getFirstSymbol()
    {
        return collect(collect($this->words)->first()->symbols)->first();
    }

    private function extractText()
    {
        $text = '';
        foreach ($this->words as $word) {
            $text .= $word->text . $word->breakCharacter;
        }
        return str_replace("\n", '', $text);
    }

    private function calculateVertices()
    {
        $wordStart = $this->words[0];
        $wordEnd = $this->words[count($this->words) - 1];

        $p1 = new Point($wordEnd->vertices->points[2]->x, $wordStart->vertices->points[0]->y);
        $p3 = new Point($wordStart->vertices->points[0]->x, $wordEnd->vertices->points[2]->y);
        return new Vertex([
            $wordStart->vertices->points[0],
            $p1,
            $wordEnd->vertices->points[2],
            $p3,
        ]);
    }
}