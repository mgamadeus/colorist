<?php

declare (strict_types=1);

namespace Colorist\ColorSpaces;

/**
 * Class LabColor
 * Represents a color in the Lab color space.
 */
class LabColor
{
    protected float $lightness;
    protected float $a;
    protected float $b;
    protected float $alpha;

    public function __construct(float $lightness, float $a, float $b, float $alpha = 1.0)
    {
        $this->lightness = $lightness;
        $this->a = $a;
        $this->b = $b;
        $this->alpha = $alpha;
    }

    public function getLightness(): float
    {
        return $this->lightness;
    }

    public function getA(): float
    {
        return $this->a;
    }

    public function getB(): float
    {
        return $this->b;
    }

    public function adjustA(float $factor): self
    {
        $newA = $this->a + $this->a * $factor / 2.0;
        return new self($this->lightness, $newA, $this->b, $this->alpha);
    }

    public function getAlpha(): float
    {
        return $this->alpha;
    }

    /**
     * Converts the current color representation to LCH color space.
     *
     * @return LchColor The color representation in LCH color space.
     */
    public function toLchColor(): LchColor
    {
        $chroma = sqrt($this->a ** 2 + $this->b ** 2);
        $hue = atan2($this->b, $this->a) * (180 / pi());
        $hue = ($hue < 0) ? $hue + 360 : $hue;

        return new LchColor($this->lightness, $chroma, $hue, $this->alpha);
    }

    /**
     * Converts the current color to XyzColor representation.
     *
     * @return XyzColor The converted XyzColor object.
     */
    public function toXyzColor(): XyzColor
    {
        $fInv = function ($value) {
            $delta = 6.0 / 29.0;
            if ($value > $delta) {
                return $value ** 3;
            } else {
                return 3 * ($delta ** 2) * ($value - 4.0 / 29.0);
            }
        };

        $x = 0.95047 * $fInv(($this->lightness + 16) / 116 + $this->a / 500);
        $y = $fInv(($this->lightness + 16) / 116);
        $z = 1.08883 * $fInv(($this->lightness + 16) / 116 - $this->b / 200);

        return new XyzColor($x, $y, $z, $this->alpha);
    }

    public function __toString(): string
    {
        return sprintf(
            'Lab(%f, %f, %f, %f)',
            $this->lightness,
            $this->a,
            $this->b,
            $this->alpha
        );
    }


    public function jsonSerialize(): array
    {
        return [
            'lightness' => $this->lightness,
            'a' => $this->a,
            'b' => $this->b,
            'alpha' => $this->alpha,
        ];
    }
}
