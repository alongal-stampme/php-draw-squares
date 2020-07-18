<?php

namespace App\NlpTools;

use NlpTools\Tokenizers\TokenizerInterface;

class CharacterTokenizer implements TokenizerInterface
{
    public function tokenize($str)
    {
        $len = mb_strlen($str, 'UTF-8');
        $result = [];
        for ($i = 0; $i < $len; $i++) {
            $result[] = mb_substr($str, $i, 1, 'UTF-8');
        }
        return $result;
    }
}