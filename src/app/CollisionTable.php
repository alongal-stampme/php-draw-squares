<?php

namespace App;

use App\Geometry\NullCollision;
use Tightenco\Collect\Support\Collection;

class CollisionTable
{
    public $word;
    public $collisions;

    private $canvas;
    private $document;

    public static function with(JsonDocument $document)
    {
        return new self($document);
    }

    public function for(WordStream $word, $canvas = null)
    {
        $this->word = $word;
        $this->canvas = $canvas;
        $collisions = $this->generateCollisions($this->word);
        $reverseCollisions = $this->generateReverseCollisions($collisions);

        $this->collisions = $collisions
            ->map(function ($collision, $key) use ($reverseCollisions) {
                $w = $this->document->text->wordStream[$key];
                $w->index = $key;
                $w->collision = $collision;
                $w->reverseCollision = $reverseCollisions;
                $reverseCollisions->count() > 0 ? $w->isReverseCollision = true : $w->isReverseCollision = false;
                return $w;
            });

        return $this->collisions;
    }

    public function doubleCheck(WordStream $word, WordStream $collisionWith, $canvas = null)
    {
        if ($word->vertices->centreLeft > $collisionWith->vertices->centreLeft) {
            if ($word->vertices->centreRight < $collisionWith->vertices->centreRight) {
                return false;
            }
            if ($canvas) {
                $canvas->draw($word->vertices->centreRight);
                $canvas->draw($collisionWith->vertices->centreRight, $canvas->colours->purple);
            }
        }

        return true;
    }

    protected function __construct(JsonDocument $document)
    {
        $this->document = $document;
        return $this;
    }

    private function generateCollisions($theWord)
    {
        $d = $this->document;
        $w = $theWord;
        $c = $this->canvas;

        if ($c) {
            $c->draw($w->getLastSymbol()->vertices, $c->colours->green);
            $c->draw($w->getFirstSymbol()->vertices, $c->colours->purple);
        }

        $collection = collect($d->text->wordStream)->map(function ($word) use ($w, $c) {
            if ($word === $w) return false;

            if ($c) {
                if ($word->text != '1/09/2020') return false;
            }

            if ($w->vertices->centreLeft->x > $word->vertices->centreLeft->x) {
                $collision = $w->getFirstSymbol()->vertices->collision(
                    $word->getLastSymbol()->vertices
                    , $c
                );
                if ($c) {
                    $c->draw($collision);
                }
                return $collision;
            }

            $collision = $w->getLastSymbol()->vertices->collision(
                $word->getFirstSymbol()->vertices
                , $c
            );
            if ($c) {
//                $c->draw($word->getFirstSymbol()->vertices);
                $c->draw($collision);
            }

            return $collision;
        });

        return $collection->filter(function ($word) {
            if (!$word) return false;
            if ($word instanceof NullCollision) return false;
            return $word;
        });
    }

    private function generateReverseCollisions(Collection $collisions)
    {
        $d = $this->document;
        $c = $this->canvas;

        return $collisions->map(function ($collision, $key) use ($c, $d) {
            $reverseCollision = $collision->destinationVertex->collision($collision->originVertex);
            if ($reverseCollision instanceof NullCollision) return false;
            return $reverseCollision;
        })
            ->filter(function ($collision) {
                if ($collision instanceof NullCollision) return false;
                return $collision;
            });
    }
}