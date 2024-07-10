<?php

declare (strict_types=1);

namespace Colorist\Palettes;

class Shades
{

    /**
     * @var array The default shade levels with their relative lightness adjustments.
     */
    public const DEFAULT_SHADES = [
        '50',
        '100',
        '200',
        '300',
        '400',
        '500',
        '600',
        '700',
        '800',
        '900'
    ];

    /**
     * @var Shade[]
     */
    protected array $shades = [];

    public function addShade(Shade $shade)
    {
        $this->shades[$shade->getGrade()] = $shade;
    }

    /**
     * @return Shade[]
     */
    public function getShades(): array
    {
        return $this->shades;
    }
}
