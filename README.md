# Colorist: A PHP Color Space Transformation Library

**Colorist** is a PHP library developed for sophisticated manipulation and conversion among various color spaces, drawing inspiration from the principles of Material Design for generating color palettes. It offers a versatile suite of functionalities for developers, designers, and artists to work with colors in a wide array of applications, from web design to digital art and beyond.

## Key Features

- **Diverse Color Spaces**: Supports numerous color spaces such as RGB, HSL, XYZ, LAB, and LCH, catering to a broad spectrum of color operations and transformations.
- **Seamless Interoperability**: Facilitates accurate and effortless conversion between color spaces, enabling complex color manipulations and analyses to fit specific requirements.
- **Material Design-Inspired Palettes and Shades**: Generate harmonious and visually appealing color palettes and shades using LCH and HSL color spaces, leveraging the logic found in Material Design guidelines. This feature is particularly beneficial for UI/UX design, branding, thematic applications, and achieving desired color gradations.
- **Customizable and Extensible**: Built to be easily extendable for incorporating additional color spaces and conversion methods, ensuring the library remains adaptable to evolving project needs and color science advancements.

## Utilizing Colorist

### Color Classes Overview

Within **Colorist**, each color space is represented by a dedicated class, equipped with methods for conversion, manipulation, and analysis. Here's a brief overview:

- `RgbColor`: Represents colors in the RGB color space, offering methods to convert to other spaces and generate hexadecimal color codes.
- `HslColor`: Handles colors in the HSL color space, allowing for intuitive adjustments of hue, saturation, and lightness.
- `XyzColor` and `LabColor`: Facilitate working within the XYZ and LAB color spaces, crucial for color science and precise color matching.
- `LchColor`: Central to generating custom palettes, this class represents the LCH color space and provides methods for palette creation inspired by Material Design.

### Generating a Palette from an RGB Color

Colorist simplifies the creation of harmonious color palettes from a single base color, drawing on Material Design principles for cohesiveness and aesthetic appeal. Here's how to generate a color palette directly from an RGB color represented in hexadecimal format:

```php
use Colorist\ColorSpaces\RgbColor;

// Create an RGB color from a hexadecimal code
$rgbColor = RgbColor::fromHex('#3498db');

// Directly generate Shades based on the RGB color
$shades = $rgbColor->createShades();

// Display the hexadecimal codes of the generated palette colors
foreach ($shades->getShades() as $shade) {
    echo $shade->getColor()->getRgbHex() . PHP_EOL;
}

This example demonstrates the library's capability to not only produce cohesive palettes but also specific shades that adhere to the Material Design guidelines, enhancing the versatility in color manipulation for your projects.

---

**Colorist** is here to simplify color manipulation and help you achieve the perfect palette for your projects, backed by the sound principles of color science and Material Design. Whether you're developing a website, creating digital art, or designing a user interface, **Colorist** offers the tools you need to work with colors more effectively and creatively.