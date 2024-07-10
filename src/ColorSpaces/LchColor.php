<?php

declare (strict_types=1);

namespace Colorist\ColorSpaces;

use Colorist\Palettes\GoldenPalette;
use Colorist\Palettes\Shades;

class LchColor
{
    protected float $lightness;
    protected float $chroma;
    protected float $hue;
    protected float $alpha;

    public function __construct(float $lightness, float $chroma, float $hue, float $alpha = 1.0)
    {
        $this->lightness = $lightness;
        $this->chroma = $chroma;
        $this->hue = $hue;
        $this->alpha = $alpha;
    }

    public function getLightness(): float
    {
        return $this->lightness;
    }

    public function getChroma(): float
    {
        return $this->chroma;
    }

    public function getHue(): float
    {
        return $this->hue;
    }

    public function getAlpha(): float
    {
        return $this->alpha;
    }

    /**
     * Convert LCH to Lab color space.
     * @return LabColor
     */
    public function toLabColor(): LabColor
    {
        $a = $this->chroma * cos(deg2rad($this->hue));
        $b = $this->chroma * sin(deg2rad($this->hue));
        return new LabColor($this->lightness, $a, $b, $this->alpha);
    }

    /**
     * Subtracts one LchColor from another.
     * @param LchColor $other
     * @return LchColor
     */
    public function subtract(LchColor $other): LchColor
    {
        return new LchColor(
            $this->lightness - $other->getLightness(),
            $this->chroma - $other->getChroma(),
            $this->hue - $other->getHue(),
            $this->alpha // Assuming alpha remains the same.
        );
    }

    /**
     * Adjusts the lightness.
     * @param callable $callback
     * @return LchColor
     */
    public function adjustLightness(callable $callback): LchColor
    {
        return new LchColor(
            $callback($this->lightness), $this->chroma, $this->hue, $this->alpha
        );
    }

    /**
     * Adjusts the chroma.
     * @param callable $callback
     * @return LchColor
     */
    public function adjustChroma(callable $callback): LchColor
    {
        return new LchColor(
            $this->lightness, $callback($this->chroma), $this->hue, $this->alpha
        );
    }

    /**
     * Adjusts the hue.
     * @param callable $callback
     * @return LchColor
     */
    public function adjustHue(callable $callback): LchColor
    {
        return new LchColor(
            $this->lightness, $this->chroma, $callback($this->hue), $this->alpha
        );
    }

    /**
     * Calculates the difference in hue between this color and another LchColor.
     * This method computes the hue difference considering the circular nature of hue values.
     * The resulting delta hue is the shortest path between the two hues on the color wheel,
     * ensuring the value falls within the range of -180 to 180 degrees. This calculation
     * is crucial for tasks that involve understanding the color relationship and difference
     * in terms of hue, such as color harmonization and gradient generation.
     *
     * @param LchColor $other The LchColor instance to compare hue with.
     * @return float The shortest difference in hue between the two colors, adjusted to fall within -180 to 180 degrees.
     */
    public function hueDelta(LchColor $other): float
    {
        $deltaHue = $this->getHue() - $other->getHue();
        if ($deltaHue > 180.0) {
            $deltaHue -= 360.0;
        } elseif ($deltaHue < -180.0) {
            $deltaHue += 360.0;
        }
        return $deltaHue;
    }

    /**
     * Calculates the mean hue value between this color and another LchColor.
     * This method averages the hue values of two colors, adjusting for the circular nature of hue values.
     * If the hue difference between the two colors is greater than 180 degrees, the calculation ensures
     * the mean hue value correctly represents the shortest distance around the hue circle. This method
     * is useful for color blending and interpolation tasks where a visually consistent hue transition is required.
     *
     * @param LchColor $other The LchColor instance to calculate the mean hue with.
     * @return float The mean hue value, adjusted for circular continuity if necessary.
     */

    public function meanHue(LchColor $other): float
    {
        $meanHue = ($this->getHue() + $other->getHue()) / 2.0;
        if (abs($this->getHue() - $other->getHue()) > 180.0) {
            $meanHue += ($meanHue < 180.0) ? 180.0 : -180.0;
        }
        return $meanHue;
    }

    /**
     * Calculates the Delta E color difference between this color and another LchColor.
     * This function implements the CIEDE2000 color difference formula, providing a more accurate
     * representation of human perception of color differences. The formula considers various aspects
     * of color difference, including lightness, chroma, and hue. This method is particularly useful
     * in contexts where an accurate color comparison is crucial, such as in color matching applications.
     *
     * @param LchColor $other The LchColor instance to compare against.
     * @return float The calculated Delta E value, representing the color difference.
     */
    public function deltaE(LchColor $other): float
    {
        $deltaLightness = $this->getLightness() - $other->getLightness();
        $meanLightness = ($this->getLightness() + $other->getLightness()) / 2.0;
        $meanChroma = ($this->getChroma() + $other->getChroma()) / 2.0;

        $aFactor = 1 - sqrt(pow($meanChroma, 7) / (pow($meanChroma, 7) + pow(25.0, 7)));
        $thisPrime = $this->toLabColor()->adjustA($aFactor)->toLchColor();
        $otherPrime = $other->toLabColor()->adjustA($aFactor)->toLchColor();

        $deltaChromaPrime = $thisPrime->getChroma() - $otherPrime->getChroma();
        $meanChromaPrime = ($thisPrime->getChroma() + $otherPrime->getChroma()) / 2.0;

        $deltaHuePrime = $thisPrime->hueDelta($otherPrime);
        $deltaHPrime = 2.0 * sqrt($thisPrime->getChroma() * $otherPrime->getChroma()) * sin(deg2rad($deltaHuePrime / 2.0));
        $meanHuePrime = $thisPrime->meanHue($otherPrime);

        $t = 1.0 - .17 * cos(deg2rad($meanHuePrime - 30.0)) + .24 * cos(deg2rad(2.0 * $meanHuePrime)) + .32 * cos(
                deg2rad(3.0 * $meanHuePrime + 6.0)
            ) - .2 * cos(deg2rad(4.0 * $meanHuePrime - 63.0));
        $sL = 1.0 + .015 * pow($meanLightness - 50.0, 2) / sqrt(20.0 + pow($meanLightness - 50.0, 2));
        $sC = 1.0 + .045 * $meanChromaPrime;
        $sH = 1.0 + .015 * $meanChromaPrime * $t;
        $rT = -2.0 * sqrt(pow($meanChromaPrime, 7) / (pow($meanChromaPrime, 7) + pow(25.0, 7))) * sin(
                deg2rad(60.0 * exp(-pow(($meanHuePrime - 275.0) / 25.0, 2)))
            );

        return sqrt(
            pow($deltaLightness / $sL, 2) + pow($deltaChromaPrime / $sC, 2) + pow(
                $deltaHPrime / $sH,
                2
            ) + $rT * ($deltaChromaPrime / $sC) * ($deltaHPrime / $sH)
        );
    }

    /**
     * Gets the closest GoldenPalette based on this color.
     *
     * @return GoldenPalette The closest GoldenPalette.
     */
    public function getClosestGoldenPalette(): GoldenPalette
    {
        $palettes = GoldenPalette::getGoldenPalettes();
        $closestPalette = null;
        $minDeltaE = PHP_FLOAT_MAX;

        foreach ($palettes as $palette) {
            $deltaE = $palette->minDeltaE($this);
            if ($deltaE < $minDeltaE) {
                $minDeltaE = $deltaE;
                $closestPalette = $palette;
            }
        }

        return $closestPalette;
    }

    /**
     * Creates shades using the closest golden palette.
     *
     * @return Shades The shades created using the closest golden palette.
     */
    public function createShades(): Shades
    {
        $closestGoldenPalette = $this->getClosestGoldenPalette();
        return $closestGoldenPalette->createCustomShades($this);
    }

    /**
     * Determines if the color is dark.
     *
     * @return bool True if the color is dark, false otherwise.
     */
    public function isDark():bool {
        return $this->getLightness() < 50;
    }

    /**
     * Checks if this color allows white text on its background.
     *
     * @return bool True if this color allows white text on its background, false otherwise.
     */
    public function allowsDarkTextOnBackgroundOfThisColor():bool {
        return $this->getLightness() > 75;
    }

    public function __toString(): string
    {
        return sprintf(
            'LCH(%f, %f, %f, %f)',
            $this->lightness,
            $this->chroma,
            $this->hue,
            $this->alpha
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'lightness' => $this->lightness,
            'chroma' => $this->chroma,
            'hue' => $this->hue,
            'alpha' => $this->alpha,
        ];
    }
}
