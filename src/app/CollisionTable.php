<?php

namespace App;

use App\Geometry\NullCollision;

class CollisionTable
{
    public $word;
    public $collisions;

    private $canvas;
    private $document;

    public static function from(JsonDocument $document)
    {
        return new self($document);
    }

    public function for(WordStream $word, $canvas = null)
    {
        $this->word = $word;
        $this->canvas = $canvas;
        $this->collisions =
            $this->generateCollisions()
                ->map(function ($collision, $key) {
                    $w = $this->document->text->wordStream[$key];
                    $w->collision = $collision;
                    return $w;
                });

        return $this->collisions;
    }

    protected function __construct(JsonDocument $document)
    {
        $this->document = $document;
        return $this;
    }

    private function generateCollisions()
    {
        $d = $this->document;
        $w = $this->word;
        $c = $this->canvas;

        if ($c) {
            $c->draw($w->getLastSymbol()->vertices, $c->colours->green);
            $c->draw($w->getFirstSymbol()->vertices, $c->colours->purple);
        }

        return collect($d->text->wordStream)->map(function ($word) use ($w) {
            if ($word === $w) return false;
            return $w->getLastSymbol()->vertices->collision(
                $word->getFirstSymbol()->vertices
            );
        })->filter(function ($word) {
            if ($word instanceof NullCollision) return false;
            return $word;
        });
    }
}