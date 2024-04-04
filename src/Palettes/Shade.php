<?php

declare (strict_types=1);

namespace Colorist\Palettes;

use Colorist\ColorSpaces\HslColor;

class Shade
{
    protected HslColor $color;
    protected string $grade;

    public function __construct(HslColor $color, string $grade)
    {
        $this->color = $color;
        $this->grade = $grade;
    }

    public function getColor(): HslColor
    {
        return $this->color;
    }

    public function setColor(HslColor $color): void
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