<?php

if ( ! function_exists('dd')) {
    function dd()
    {
        array_map(function ($x) {
            dump($x);
        }, func_get_args());
        die;
    }
}

if ( ! function_exists('load_json_file')) {
    function load_json_file($file)
    {
        $json = file_get_contents(__DIR__ . '/../json_files/' . $file);
        return json_decode($json);
    }
}