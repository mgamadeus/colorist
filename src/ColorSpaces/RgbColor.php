<?php

declare (strict_types=1);

namespace Colorist\ColorSpaces;

use Colorist\Palettes\Shades;
use InvalidArgumentException;

class RgbColor
{
    protected float $red;
    protected float $green;
    protected float $blue;
    protected float $alpha;

    public function __construct(float $red, float $green, float $blue, float $alpha = 1.0)
    {
        $this->red = $this->clamp($red);
        $this->green = $this->clamp($green);
        $this->blue = $this->clamp($blue);
        $this->alpha = $this->clamp($alpha);
    }

    private function clamp(float $value): float
    {
        return max(0.0, min($value, 1.0));
    }

    public function getRed(): float
    {
        return $this->red;
    }

    public function getGreen(): float
    {
        return $this->green;
    }

    public function getBlue(): float
    {
        return $this->blue;
    }

    public function getAlpha(): float
    {
        return $this->alpha;
    }

    /**
     * Creates an RgbColor instance from RGBA values.
     *
     * @param int $red The red component (0-255).
     * @param int $green The green component (0-255).
     * @param int $blue The blue component (0-255).
     * @param float $alpha The alpha component (0.0-1.0).
     * @return RgbColor The new RgbColor instance.
     */
    public static function fromRgba(int $red, int $green, int $blue, float $alpha): RgbColor
    {
        return new self($red / 255, $green / 255, $blue / 255, $alpha);
    }


    /**
     * Converts the RGB color to HSL color representation.
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
                $hue = 60 * fmod((($this->green - $this->blue) / $delta), 6);
            } elseif ($max == $this->green) {
                $hue = 60 * (($this->blue - $this->red) / $delta + 2);
            } elseif ($max == $this->blue) {
                $hue = 60 * (($this->red - $this->green) / $delta + 4);
            }
        }

        $hue = fmod(($hue + 360), 360); // Ensure hue is between 0-360 degrees
        $lightness = ($max + $min) / 2;
        $saturation = $delta == 0 ? 0 : $delta / (1 - abs(2 * $lightness - 1));

        // The HSL components are now floating-point values
        return new HslColor($hue, $saturation, $lightness, $this->alpha);
    }

    /**
     * Converts the color to the Lab color space.
     *
     * This method converts the color from its current color space to the Lab color space.
     *
     * @return LabColor The color representation in the Lab color space.
     */
    public function toLabColor(): LabColor
    {
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
    public function toLchColor(): LchColor
    {
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
        $customPaletteRgb = array_map(function ($lchColor) {
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
        return sprintf('#%02X%02X%02X', round($this->red * 255), round($this->green * 255), round($this->blue * 255));
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
        return sprintf('#%02X%02X%02X%02X', round($this->red * 255), round($this->green * 255), round($this->blue * 255), round($this->alpha * 255));
    }

    /**
     * Converts the object to a string representation.
     *
     * This method checks if the alpha value is 1 (fully opaque). If it is true, it uses the getRgbHex() method
     * to return the RGB value as a hexadecimal string. If the alpha value is false, it uses the getRgbaHex() method
     * to return the RGBA value as a hexadecimal string.
     *
     * @return string The string representation of the object.
     */
    public function __toString(): string
    {
        // Check if alpha value is 1 (fully opaque).
        // If true, use getRgbHex() for RGB output.
        // If false, use getRgbaHex() for RGBA output.
        return $this->alpha == 1.0 ? $this->getRgbHex() : $this->getRgbaHex();
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
        return sprintf('#%02X%02X%02X%02X', round($this->alpha * 255), round($this->red * 255), round($this->green * 255), round($this->blue * 255));
    }


    /**
     * Returns the CSS rgba() representation of the color.
     *
     * This method constructs a string suitable for CSS usage, representing the color in the rgba() format.
     * It uses the red, green, and blue components directly and formats the alpha value as a decimal between 0 and 1,
     * conforming to the CSS color specification.
     *
     * @return string The CSS rgba() color representation.
     */
    public function getCssRgba(): string
    {
        $r = round($this->red * 255);
        $g = round($this->green * 255);
        $b = round($this->blue * 255);
        return sprintf('rgba(%d, %d, %d, %.2f)', $r, $g, $b, $this->alpha);
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

        // Convert hex values to decimal and normalize to [0, 1]
        $r = hexdec(substr($hex, 0, 2)) / 255.0;
        $g = hexdec(substr($hex, 2, 2)) / 255.0;
        $b = hexdec(substr($hex, 4, 2)) / 255.0;
        $a = hexdec(substr($hex, 6, 2)) / 255.0;

        return new self($r, $g, $b, $a);
    }


    /**
     * Generates shades of the color.
     *
     * This method converts the color to HSL format using the toHslColor() method,
     * and then generates shades of the color using the generateShades() method of the HSL color.
     * The generated shades are returned as a Shades object.
     *
     * @return Shades The shades of the color.
     */
    public function generateShades(): Shades
    {
        return $this->toHslColor()->generateShades();
    }
}