<?php

namespace App\NlpTools;

use NlpTools\Tokenizers\TokenizerInterface;

class GoogleOcrTokenizer implements TokenizerInterface
{
    public function tokenize($str)
    {
        $array = $this->replaceCharFromArray(':', [$str]);
        $array = $this->replaceCharFromArray('/', $array);
        $array = $this->replaceCharFromArray('*', $array);
        $array = $this->replaceCharFromArray('(', $array);
        $array = $this->replaceCharFromArray(')', $array);
        return $array;
    }

    private function replaceCharFromString($char, $str)
    {
        $original = $char;
        if ($char === '/') $char = '\/';
        $pat = "/[{$char}]|[ ]/";

        if ($str === $original) return [$str];
        $str = str_replace($original, "{$original}{$original}", $str);
        $str = str_replace("{$original} ", "$original", $str);

        // remove the first character if it is equal to our original $char
        if (substr($str, 0, 1) === "{$original}") $str = substr($str, 1);

        // remove the last character if it is equal to our original $char
        if (substr($str, -1) === "{$original}") $str = substr($str, 0, -1);

        $array = preg_split($pat, $str);

        foreach ($array as $i => $item) {
            if ($item === '') $array[$i] = "{$original}";
        }
        return $array;
    }

    private function replaceCharFromArray($char, $array)
    {
        $newArray = [];
        foreach ($array as $item) {
            if (str_contains($item, "{$char}")) {
                $item = $this->replaceCharFromString($char, $item);
            }
            $newArray[] = $item;
        }

        return collect($newArray)->flatten()->toArray();
    }
}