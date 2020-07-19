<?php

namespace App;

use App\Geometry\Vertex;

class Symbol
{
    protected $json;
    public $text;
    public $language;
    public $confidence;
    public $break;
    public $vertices;

    public function __construct($json = null)
    {
        $this->text = '';
        $this->vertices = new Vertex();
        $this->confidence = 0.0;
        $this->break = null;

        if ($json) $this->setJson($json);
    }

    public function setJson($json)
    {
        $this->json = $json;
        $this->text = $json->text;
        $this->confidence = $json->confidence;
        $this->language = $json->property->detectedLanguages[0]->languageCode;
        $this->vertices->fromJson($json->boundingBox->vertices);
        $this->setBreak();
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function merge(Word $word)
    {
        $outcome = new Word();
        $outcome->text = $this->text . $word->text;
        $outcome->vertices[0] = $this->vertices[0];
        $outcome->vertices[1] = $word->vertices[1];
        $outcome->vertices[2] = $word->vertices[2];
        $outcome->vertices[3] = $this->vertices[3];
        return $outcome;
    }

    public function breakCharacter()
    {
        $breaks = [
            'LINE_BREAK' => "\n",
            'EOL_SURE_SPACE' => "\n",
            'SPACE' => " "
        ];
        return $breaks[$this->break];
    }

    private function extractText()
    {
        $text = '';
        foreach ($this->jsonWord->symbols as $symbol) {
            $text .= $symbol->text;
        }
        $this->text = $text;
    }

    private function setBreak()
    {
        $this->break = $this->json->property->detectedBreak->type;
    }
}