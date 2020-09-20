<?php

namespace App;

class CollisionTable
{
    public $word;
    public $collisions;

    public static function makeFor(WordStream $word)
    {
        return new self($word);
    }

    protected function __construct(WordStream $word)
    {
        $this->word = $word;
        $this->collisions = [];
        $this->generateCollisionTable();
    }

    private function generateCollisionTable()
    {

    }
}