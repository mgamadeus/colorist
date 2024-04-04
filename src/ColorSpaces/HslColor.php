<?php

declare (strict_types=1);

/**
 * Class HslColor
 *
 * Represents a color in the HSL color space.
 */

namespace Colorist\ColorSpaces;

/**
 * Class HslColor
 *
 * Represents a color in the HSL color model.
 */
class HslColor
{
    protected int $hue;
    protected float $saturation;
    protected float $lightness;
    protected float $alpha;

    public function __construct(int $hue, float $saturation, float $lightness, float $alpha = 1.0)
    {
        $this->hue = $hue;
        $this->saturation = $saturation;
        $this->lightness = $lightness;
        $this->alpha = $alpha;
    }

    public function getHue(): int
    {
        return $this->hue;
    }

    public function getSaturation(): float
    {
        return $this->saturation;
    }

    public function getLightness(): float
    {
        return $this->lightness;
    }

    public function getAlpha(): float
    {
        return $this->alpha;
    }

    /**
     * Converts the current color to an RGB color.
     *
     * @return RgbColor The RGB color representation of the current color.
     */
    public function toRgbColor(): RgbColor
    {
        $chroma = (1 - abs(2 * $this->lightness - 1)) * $this->saturation;
        $huePrime = $this->hue / 60.0;
        $x = $chroma * (1 - abs(fmod($huePrime, 2) - 1));
        $m = $this->lightness - $chroma / 2;

        $r = $g = $b = 0;

        if ($huePrime < 1.0) {
            $r = $chroma;
            $g = $x;
        } elseif ($huePrime < 2.0) {
            $r = $x;
            $g = $chroma;
        } elseif ($huePrime < 3.0) {
            $g = $chroma;
            $b = $x;
        } elseif ($huePrime < 4.0) {
            $g = $x;
            $b = $chroma;
        } elseif ($huePrime < 5.0) {
            $r = $x;
            $b = $chroma;
        } elseif ($huePrime <= 6.0) {
            $r = $chroma;
            $b = $x;
        }

        $r += $m;
        $g += $m;
        $b += $m;

        return new RgbColor($r, $g, $b, $this->alpha);
    }
}
