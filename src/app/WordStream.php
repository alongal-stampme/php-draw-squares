<?php

namespace App;

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

        return new Vertex([
            $wordStart->vertices->points[0],
            $wordEnd->vertices->points[1],
            $wordEnd->vertices->points[2],
            $wordStart->vertices->points[3],
        ]);
    }
}