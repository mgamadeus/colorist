<?php

declare (strict_types=1);

namespace Colorist\ColorSpaces;

class XyzColor {
    protected float $x;
    protected float $y;
    protected float $z;
    protected float $alpha;

    public function __construct(float $x, float $y, float $z, float $alpha = 1.0) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->alpha = $alpha;
    }

    public function getX(): float {
        return $this->x;
    }

    public function getY(): float {
        return $this->y;
    }

    public function getZ(): float {
        return $this->z;
    }

    public function getAlpha(): float {
        return $this->alpha;
    }

    /**
     * Converts the current color to Lab color space.
     *
     * @return LabColor The color object representing the Lab values.
     */
    public function toLabColor(): LabColor {
        $f = function ($t) {
            $delta = 6.0 / 29.0;
            if ($t > pow($delta, 3)) {
                return pow($t, 1 / 3);
            } else {
                return $t / (3 * pow($delta, 2)) + 4 / 29;
            }
        };

        $lightness = 116.0 * $f($this->y) - 16;
        $a = 500.0 * ($f($this->x / 0.95047) - $f($this->y));
        $b = 200.0 * ($f($this->y) - $f($this->z / 1.08883));

        return new LabColor($lightness, $a, $b, $this->alpha);
    }

    /**
     * Converts the current color to RGB color.
     *
     * This method applies reverse gamma correction formula to convert the current color to RGB color.
     * The reverseGammaCorrection function is used to calculate the RGB values for each component.
     * It then scales these values to the 0-255 range suitable for the RgbColor constructor and ensures
     * they are integers.
     *
     * @return RgbColor The RGB representation of the current color.
     */
    public function toRgbColor(): RgbColor {
        $reverseGammaCorrection = function (float $c): float {
            if ($c <= 0.0031308) {
                return 12.92 * $c;
            } else {
                return 1.055 * pow($c, 1 / 2.4) - 0.055;
            }
        };

        // Convert XYZ to linear RGB in the range 0-1
        $r = $reverseGammaCorrection(3.2404542 * $this->x + -1.5371385 * $this->y + -0.4985314 * $this->z);
        $g = $reverseGammaCorrection(-0.9692660 * $this->x + 1.8760108 * $this->y + 0.0415560 * $this->z);
        $b = $reverseGammaCorrection(0.0556434 * $this->x + -0.2040259 * $this->y + 1.0572252 * $this->z);

        // Scale the values to the range 0-255 and round to integers
        $r = round($r * 255);
        $g = round($g * 255);
        $b = round($b * 255);

        // Ensure values are clamped to the range 0-255
        $r = min(255, max(0, $r));
        $g = min(255, max(0, $g));
        $b = min(255, max(0, $b));

        return new RgbColor((int)$r, (int)$g, (int)$b, $this->alpha);
    }
}
