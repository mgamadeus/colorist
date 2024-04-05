<?php

declare (strict_types=1);

namespace Colorist\Palettes;

use Colorist\ColorSpaces\HslColor;

class Shades
{

    /**
     * @var array The default shade levels with their relative lightness adjustments.
     */
    public const DEFAULT_SHADES = [
        '50' => 74,
        '100' => 59,
        '200' => 48,
        '300' => 34,
        '400' => 28,
        '500' => 22,
        '600' => 16,
        '700' => 10,
        '800' => 4,
        '900' => 0,
        'A100' => 46,
        'A200' => 38,
        'A400' => 21,
        'A700' => 10,
    ];

    /**
     * @var Shade[]
     */
    private array $shades = [];

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

    /**
     * Generates a set of shades from a base HSL color.
     *
     * @param HslColor $baseColor The base HSL color.
     * @return Shades A new Shades instance populated with generated shades.
     */
    public static function generateFromBase(HslColor $baseColor): Shades
    {
        $instance = new self();
        foreach (self::DEFAULT_SHADES as $grade => $lightnessAdjustment) {
            $newLightness = $baseColor->getLightness() + ($lightnessAdjustment - $baseColor->getLightness()) * ((int)$grade / 100.0);
            $shadeColor = new HslColor($baseColor->getHue(), $baseColor->getSaturation(), $newLightness, $baseColor->getAlpha());
            $instance->addShade(new Shade($shadeColor, (string) $grade));
        }
        return $instance;
    }

}
