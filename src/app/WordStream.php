<?php

namespace App;

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

        $outcome = new WordStream(
            array_merge($this->words, $wordStream->words)
        );
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
        $merged = $wordStart->merge($wordEnd);
        return $merged->vertices;
    }
}