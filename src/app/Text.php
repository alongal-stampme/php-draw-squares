<?php

namespace App;

use App\Geometry\Point;
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

    public function characterCount()
    {
        $count = 0;
        foreach ($this->wordStream as $ws) {
            $count += $ws->characterCount();
        }
        return $count;
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

        $p1 = new Point($wordEnd->vertices->points[2]->x, $wordStart->vertices->points[0]->y);
        $p3 = new Point($wordStart->vertices->points[0]->x, $wordEnd->vertices->points[2]->y);
        return new Vertex([
            $wordStart->vertices->points[0],
            $p1,
            $wordEnd->vertices->points[2],
            $p3,
        ]);
    }

    public function sortByCharacterLength($method = 'asc')
    {
        $result = collect($this->wordStream)->map(function ($ws) {
            return strlen($ws->text);
        })->sort();
        if ($method == 'desc') $result = $result->sortDesc();

        $array = [];
        foreach ($result as $index => $ws) {
            $array[] = $this->wordStream[$index];
        }
        return $array;
    }
}