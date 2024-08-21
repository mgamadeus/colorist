<?php

declare (strict_types=1);

/**
 * Class HslColor
 *
 * Represents a color in the HSL color space.
 */

namespace Colorist\ColorSpaces;

use Colorist\Palettes\Shades;

/**
 * Class HslColor
 *
 * Represents a color in the HSL color model.
 */
class HslColor
{
    protected float $hue;
    protected float $saturation;
    protected float $lightness;
    protected float $alpha;

    public function __construct(float $hue, float $saturation, float $lightness, float $alpha = 1.0)
    {
        $this->hue = $hue;
        $this->saturation = $saturation;
        $this->lightness = $lightness;
        $this->alpha = $alpha;
    }

    public function getHue(): float
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

    public function setHue(float $hue): void
    {
        $this->hue = $hue;
    }

    public function setSaturation(float $saturation): void
    {
        $this->saturation = $saturation;
    }

    public function setLightness(float $lightness): void
    {
        $this->lightness = $lightness;
    }

    public function setAlpha(float $alpha): void
    {
        $this->alpha = $alpha;
    }

    /**
     * Converts the current color to an RGB color.
     *
     * This method applies HSL to RGB conversion formulas to convert the current HSL color
     * values to the RGB color space, considering the alpha value. The resulting RGB values
     * are in the range of 0 to 1, suitable for consistent digital color representation across
     * various systems that use floating-point numbers for color values.
     *
     * @return RgbColor The RGB color representation of the current HSL color.
     */
    public function toRgbColor(): RgbColor
    {
        if ($this->saturation == 0) {
            // Achromatic color (gray scale)
            return new RgbColor($this->lightness, $this->lightness, $this->lightness, $this->alpha);
        } else {
            $functionHueToRgb = function ($p, $q, $t) {
                if ($t < 0) $t += 1;
                if ($t > 1) $t -= 1;
                if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
                if ($t < 1/2) return $q;
                if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
                return $p;
            };
            // Normalize hue to [0, 1]
            $hue = $this->hue / 360;

            $q = $this->lightness < 0.5 ? $this->lightness * (1 + $this->saturation) : $this->lightness + $this->saturation - $this->lightness * $this->saturation;
            $p = 2 * $this->lightness - $q;

            $r = $functionHueToRgb($p, $q, $hue + 1/3);
            $g = $functionHueToRgb($p, $q, $hue);
            $b = $functionHueToRgb($p, $q, $hue - 1/3);
        }

        // The RGB values are already in the correct 0-1 range
        return new RgbColor($r, $g, $b, $this->alpha);
    }


    /**
     * Generates shades based on the current color.
     *
     * @return Shades The generated shades based on the current color.
     */
    public function createShades(): Shades
    {
        return $this->toRgbColor()->createShades();
    }

    public function __toString(): string
    {
        return sprintf(
            'HSL(%f, %f, %f, %f)',
            $this->hue,
            $this->saturation,
            $this->lightness,
            $this->alpha
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'hue' => $this->hue,
            'saturation' => $this->saturation,
            'lightness' => $this->lightness,
            'alpha' => $this->alpha,
        ];
    }
}
