<?php

namespace App\Geometry;

class LineStream
{
    public $lines;

    public function __construct(array $lines = null)
    {
        $this->lines = $lines;
    }

    public function add(Line $line)
    {
        $this->lines[] = $line;
    }

    public function diff($lineIndex1, $lineIndex2)
    {
        dump($this->lines[$lineIndex1]);
        dump($this->lines[$lineIndex2]);
    }
}