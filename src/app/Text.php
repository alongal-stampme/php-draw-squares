<?php

namespace App;

use App\Geometry\Vertex;

class Text
{
    protected $jsonBlocks;
    public $wordStream;
    public $vertices;

    public function __construct()
    {
        $this->jsonBlocks = [];
        $this->vertices = new Vertex();
        $this->wordStream = [];
    }

    public function fromWords(array $words)
    {
        $this->wordStream = $this->extractWordStream($words);
        $this->vertices = $this->calculateVertices();
        return $this;
    }

    private function extractWordStream($words)
    {
        $array = [];
        $text = [];
        foreach ($words as $word) {
            $text[] = $word;
            if ($word->breakCharacter == "\n") {
                $array[] = new WordStream($text);
                $text = [];
            }
        }
        return $array;
    }

    private function calculateVertices()
    {
        $wordStart = $this->wordStream[0];
        $wordEnd = $this->wordStream[count($this->wordStream) - 1];

        return new Vertex([
            $wordStart->vertices->points[0],
            $wordEnd->vertices->points[1],
            $wordEnd->vertices->points[2],
            $wordStart->vertices->points[3],
        ]);
    }
}