<?php

declare (strict_types=1);

namespace Colorist\Palettes;

use Colorist\ColorSpaces\HslColor;
use Colorist\ColorSpaces\RgbColor;

class Shade
{
    protected RgbColor $color;
    protected string $grade;

    public function __construct(RgbColor $color, string $grade)
    {
        $this->color = $color;
        $this->grade = $grade;
    }

    public function getColor(): RgbColor
    {
        return $this->color;
    }

    public function setColor(RgbColor $color): void
    {
        $this->color = $color;
    }

    public function getGrade(): string
    {
        return $this->grade;
    }

    public function setGrade(string $grade): void
    {
        $this->grade = $grade;
    }
}