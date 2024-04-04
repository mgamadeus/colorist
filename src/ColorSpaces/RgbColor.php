<?php

declare (strict_types=1);

namespace Colorist\ColorSpaces;

use InvalidArgumentException;

class RgbColor
{
    protected int $red;
    protected int $green;
    protected int $blue;
    protected float $alpha;

    public function __construct(int $red, int $green, int $blue, float $alpha = 1.0)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
        $this->alpha = $alpha;
    }

    public function getRed(): int
    {
        return $this->red;
    }

    public function getGreen(): int
    {
        return $this->green;
    }

    public function getBlue(): int
    {
        return $this->blue;
    }

    public function getAlpha(): float
    {
        return $this->alpha;
    }

    /**
     * Converts the RGB color to HSL color representation.
     *
     * This method calculates and returns the HSL color representation of the RGB color.
     * The HSL color model represents color based on its hue, saturation, and lightness components.
     * The hue value represents the color itself, saturation represents the purity of the color, and
     * lightness represents the brightness of the color.
     *
     * @return HslColor The HSL color representation of the RGB color.
     */
    public function toHslColor(): HslColor
    {
        $max = max($this->red, $this->green, $this->blue);
        $min = min($this->red, $this->green, $this->blue);
        $delta = $max - $min;

        $hue = 0;
        if ($delta != 0) {
            if ($max == $this->red) {
                $hue = 60 * (($this->green - $this->blue) / $delta);
            } elseif ($max == $this->green) {
                $hue = 60 * (($this->blue - $this->red) / $delta + 2);
            } elseif ($max == $this->blue) {
                $hue = 60 * (($this->red - $this->green) / $delta + 4);
            }
        }
        $hue = fmod(($hue + 360), 360);

        $saturation = 0;
        if (!($max == 0 || $min == 1)) {
            $saturation = $delta / (1 - abs($max + $min - 1));
        }

        $lightness = ($max + $min) / 2;

        return new HslColor((int)$hue, $saturation, $lightness, $this->alpha);
    }

    /**
     * Converts the color to the Lab color space.
     *
     * This method converts the color from its current color space to the Lab color space.
     *
     * @return LabColor The color representation in the Lab color space.
     */
    public function toLabColor(): LabColor {
        return $this->toXyzColor()->toLabColor();
    }

    /**
     * Converts the color to LchColor representation.
     *
     * The method converts the current color to LchColor representation.
     * It first converts the color to LabColor representation using the toLabColor() method,
     * then converts the LabColor to LchColor using the toLchColor() method.
     *
     * @return LchColor The color in LchColor representation.
     */
    public function toLchColor(): LchColor {
        return $this->toLabColor()->toLchColor();
    }

    /**
     * Creates a color palette based on the current RGB color.
     *
     * @return RgbColor[] Returns an array of RgbColor objects representing the palette.
     */
    public function createPalette(): array
    {
        // Convert RGB to LCH
        $lchColor = $this->toLchColor();

        // Get the closest golden palette and create a custom palette based on the LCH color
        $closestGoldenPalette = $lchColor->getClosestGoldenPalette();
        $customPaletteLch = $closestGoldenPalette->createCustomPalette($lchColor);

        // Convert the custom palette from LCH back to RGB
        $customPaletteRgb = array_map(function($lchColor) {
            return $lchColor->toLabColor()->toXyzColor()->toRgbColor();
        }, $customPaletteLch);

        return $customPaletteRgb;
    }

    /**
     * Converts the RGB color to the XYZ color space.
     *
     * The method calculates the XYZ color coordinates of the RGB color based on the defined formulas.
     * It performs gamma correction on the red, green, and blue components of the color and then calculates
     * the X, Y, and Z coordinates using the specified formulas: X = 0.4124564 * R + 0.3575761 * G + 0.1804375 * B,
     * Y = 0.2126729 * R + 0.7151522 * G + 0.0721750 * B, Z = 0.0193339 * R + 0.1191920 * G + 0.9503041 * B.
     * The resulting XYZ color is then encapsulated in an instance of the XyzColor class with the same alpha value.
     *
     * @return XyzColor The XYZ representation of the RGB color.
     */
    public function toXyzColor(): XyzColor
    {
        $r = $this->correctGamma($this->red);
        $g = $this->correctGamma($this->green);
        $b = $this->correctGamma($this->blue);

        $x = 0.4124564 * $r + 0.3575761 * $g + 0.1804375 * $b;
        $y = 0.2126729 * $r + 0.7151522 * $g + 0.0721750 * $b;
        $z = 0.0193339 * $r + 0.1191920 * $g + 0.9503041 * $b;

        return new XyzColor($x, $y, $z, $this->alpha);
    }

    // Die Implementierung der toXyzColor Methode würde ähnlich erfolgen, benötigt allerdings eine entsprechende XyzColor Klasse

    /**
     * Returns the hexadecimal representation of the RGB color.
     *
     * The method returns a string representing the RGB color in hexadecimal format.
     * The format is as follows: #RRGGBB, where RR, GG, and BB are two-digit hexadecimal values representing
     * the red, green, and blue components of the color respectively.
     *
     * @return string The hexadecimal representation of the RGB color.
     */
    public function getRgbHex(): string
    {
        return sprintf('#%02X%02X%02X', (int)($this->red * 255), (int)($this->green * 255), (int)($this->blue * 255));
    }

    /**
     * Returns the hexadecimal representation of the RGBA color.
     *
     * The method returns a string representing the RGBA color in hexadecimal format.
     * The format is as follows: #RRGGBBAA, where RR, GG, BB, and AA are two-digit hexadecimal values representing
     * the red, green, blue, and alpha components of the color respectively.
     *
     * @return string The hexadecimal representation of the RGBA color.
     */
    public function getRgbaHex(): string
    {
        return sprintf('#%02X%02X%02X%02X', (int)($this->red * 255), (int)($this->green * 255), (int)($this->blue * 255), (int)($this->alpha * 255));
    }

    /**
     * Returns the hexadecimal representation of the ARGB color.
     *
     * The method returns a string representing the ARGB color in hexadecimal format.
     * The format is as follows: #AARRGGBB, where AA, RR, GG, and BB are two-digit hexadecimal values representing
     * the alpha, red, green, and blue components of the color respectively.
     *
     * @return string The hexadecimal representation of the ARGB color.
     */
    public function getArgbHex(): string
    {
        return sprintf('#%02X%02X%02X%02X', (int)($this->alpha * 255), (int)($this->red * 255), (int)($this->green * 255), (int)($this->blue * 255));
    }

    /**
     * Corrects the gamma value of the given input value.
     *
     * This method corrects the gamma value of the input value based on the following formula:
     * If the input value is less than or equal to 0.04045, the corrected value is obtained by dividing the input value by 12.92.
     * Otherwise, the corrected value is obtained by calculating (input value + 0.055) / 1.055 raised to the power of 2.4.
     *
     * @param float $value The input value to correct the gamma value.
     * @return float The corrected gamma value of the input value.
     */
    protected function correctGamma(float $value): float
    {
        return $value <= 0.04045 ? $value / 12.92 : pow(($value + 0.055) / 1.055, 2.4);
    }

    // Existing code...

    /**
     * Creates an RGB color from a hex string.
     *
     * @param string $hex The hex color string.
     * @return self
     */
    public static function fromHex(string $hex): self
    {
        // Strip any leading hash
        $hex = ltrim($hex, '#');

        // Ensure hex is 6 or 8 characters
        if (strlen($hex) === 6) {
            $hex .= 'FF'; // Assume opaque if alpha is not provided
        }

        if (strlen($hex) !== 8) {
            throw new InvalidArgumentException('Hex color must be 6 or 8 characters long.');
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $a = hexdec(substr($hex, 6, 2)) / 255;

        return new self($r, $g, $b, $a);
    }
}