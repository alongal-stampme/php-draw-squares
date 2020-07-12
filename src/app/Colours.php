<?php

namespace App;

class Colours
{
    public $pink;
    public $white;
    public $green;
    public $lightGray;
    public $red;
    public $purple;
    public $yellow;

    public function __construct($canvas)
    {
        $this->pink = imagecolorclosest($canvas, 255, 105, 180);
        $this->white = imagecolorclosest($canvas, 255, 255, 255);
        $this->green = imagecolorclosest($canvas, 132, 135, 28);
        $this->lightGray = imagecolorclosest($canvas, 242, 243, 244);
        $this->red = imagecolorclosest($canvas, 255, 0, 0);
        $this->purple = imagecolorclosest($canvas, 148, 0, 211);
        $this->yellow = imagecolorclosest($canvas, 255, 255, 0);
    }
}