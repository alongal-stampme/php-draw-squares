<?php

namespace App;

use App\Geometry\Vertex;

class JsonDocument
{
    public $json;
    public $width;
    public $height;
    public $words;
    public $text;
    public $vertices;

    public function __construct($json)
    {
        $this->json = $json;
        $this->width = $this->json->responses[0]->fullTextAnnotation->pages[0]->width;
        $this->height = $this->json->responses[0]->fullTextAnnotation->pages[0]->height;
        $this->words = $this->generateWords();
        $this->text = $this->generateText();
        $this->vertices = (new Vertex())->fromJson($this->json->responses[0]->textAnnotations[0]->boundingPoly->vertices);
    }

    public function sortByYAxis()
    {
        $result = collect($this->text->wordStream)->sortBy(function ($wordStream) {
            return $wordStream->vertices->centre->y;
        });
        $this->text->wordStream = $result->toArray();
    }

    public function sortByXAxis()
    {
        $result = collect($this->text->wordStream)->sortBy(function ($wordStream) {
            return $wordStream->vertices->centre->x;
        });
        $this->text->wordStream = $result->toArray();
    }

    public function characterCount()
    {
        return $this->text->characterCount();
    }

    public function writeToFile(array $data, $fileName)
    {
        // Init multi dimensional array
        $text = [];
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < 100; $j++) {
                $text[$i][$j] = ' ';
            }
        }

        // Copy text into array
        foreach ($data as $line) {
            for ($i = 0; $i < strlen($line['text']); $i++) {
                $y = (int)$line['y'];
                $x = (int)$line['x'];
                $text[$y][$x + $i] = $line['text'][$i];
            }
        }
        // Write to file
        $file = fopen("output.txt", "w+");
        foreach ($text as $line) fwrite($file, implode('', $line) . PHP_EOL);
        fclose($file);

        return file_get_contents($fileName);
    }

    private function generateWords()
    {
        $array = [];

        foreach ($this->json->responses[0]->fullTextAnnotation->pages[0]->blocks as $block) {
            foreach ($block->paragraphs as $paragraph) {
                foreach ($paragraph->words as $word) {
//                    if ($word->confidence < 0.8) continue;
                    $array[] = new Word($word);
                }
            }
        }

        return $array;
    }

    private function generateText()
    {
        return (new Text)->fromWords($this->words);
    }
}