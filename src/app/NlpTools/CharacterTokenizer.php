<?php

namespace App\NlpTools;

use NlpTools\Tokenizers\TokenizerInterface;

class CharacterTokenizer implements TokenizerInterface
{
    public function tokenize($str)
    {
        return str_split($str);
    }
}