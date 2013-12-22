<?php
/**
 * This file is part of the ImagePalette package.
 *
 * (c) Brian Foxwell <brian@foxwell.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bfoxwell\ImagePalette;

use Bfoxwell\ImagePalette\Exception\UnsupportedFileTypeException;

/**
 * Class ImagePalette
 *
 * Gets the prominent colors in a given image. To get common color matching, all pixels are matched
 * against a white-listed color palette.
 *
 * @package bfoxwell\ImagePalette
 */
class ImagePalette
{
    /**
     * File or Url
     * @var string
     */
    public $file;

    /**
     * Loaded Image
     * @var object
     */
    public $loadedImage;

    /**
     * Loaded Image Colors in Hex
     * @var array
     */
    public $loadedImageColors = array();

    /**
     * Width of image
     * @var integer
     */
    public $width;

    /**
     * Height of image
     * @var integer
     */
    public $height;

    /**
     * Number of colors to return
     * @var integer
     */
    public $numColorsOnPalette;

    /**
     * Hex Whitelist
     * @var array
     */
    public $hexWhiteList = array(
        "#660000", "#990000", "#cc0000", "#cc3333", "#ea4c88", "#993399",
        "#663399", "#333399", "#0066cc", "#0099cc", "#66cccc", "#77cc33",
        "#669900", "#336600", "#666600", "#999900", "#cccc33", "#ffff00",
        "#ffcc33", "#ff9900", "#ff6600", "#cc6633", "#996633", "#663300",
        "#000000", "#999999", "#cccccc", "#ffffff", "#E7D8B1", "#FDADC7",
        "#424153", "#ABBCDA", "#F5DD01"
    );

    /**
     * RGB Whitelist
     * @var array
     */
    public $RGBWhiteList = array();


    /**
     * Constructor
     * @param string $image
     * @param int $precision
     * @param int $numColorsOnPalette
     * @param bool $betterDifferencing
     */
    public function __construct($image, $precision = 10, $numColorsOnPalette = 5)
    {
        $this->file = $image;
        $this->precision = $precision;
        $this->numColorsOnPalette = $numColorsOnPalette;
        $this->setRGBWhiteList();
        $this->setWorkingImageGD($this->file);
        $this->readPixelsGD();
    }

    /**
     * Create an array of Hex and RGB values from Hex Whitelist
     * @return array
     */
    private function setRGBWhiteList()
    {
        foreach ($this->hexWhiteList as $hex) {
            $this->RGBWhiteList[] = $this->HexToRGB($hex);
        }
    }

    /**
     * Convert Hex to RGB
     * @todo Move to ColorConversions File
     * @param $hex
     * @return array
     */
    private function HexToRGB($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        $rgb = array($r, $g, $b);

        return $rgb; // returns an array with the rgb values
    }

    /**
     * Load and set the working image.
     * @param $image
     */
    private function setWorkingImageGD($image)
    {
        $pathArray = explode('.', $image);
        $fileExtension = end($pathArray);

        try {

            switch ($fileExtension) {
                case "png":
                    $imageCreate = "imagecreatefrompng";
                    break;
                case "jpg":
                    $imageCreate = "imagecreatefromjpeg";
                    break;
                case "gif":
                    $imageCreate = "imagecreatefromgif";
                    break;
                case "bmp":
                    $imageCreate = "imagecreatefrombmp";
                    break;
                default:
                    throw new UnsupportedFileTypeException("The file type .$fileExtension is not supported.");
            }

            // Set working Image
            $this->loadedImage = $imageCreate($image);

            // Set Image Size
            $this->setImageSizeGD();

        } catch (UnsupportedFileTypeException $e) {
            echo $e->getMessage() . "\n";
            exit();
        }
    }

    /**
     * Get and set size of the image.
     */
    private function setImageSizeGD()
    {
        $dimensions = getimagesize($this->file);
        $this->width = $dimensions[0];
        $this->height = $dimensions[1];
    }

    /**
     * Read pixels using GD and push each matching hex value into array.
     */
    public function readPixelsGD()
    {
        for ($x = 0; $x < $this->width; $x += $this->precision) { // Row
            for ($y = 0; $y < $this->height; $y += $this->precision) { // Column
                $index = imagecolorat($this->loadedImage, $x, $y);

                // Detect and set transparent value
                if ($this->detectTransparencyGD($index)) {
                    $this->loadedImageColors[] = "transparent";
                    continue;
                }

                $rgb = imagecolorsforindex($this->loadedImage, $index);

                $this->loadedImageColors[] = $this->getClosestColor($rgb["red"], $rgb["green"], $rgb["blue"]);
            }
        }
    }

    /**
     * Detect Transparency using GD
     * @param $rgba
     * @return bool
     */
    public function detectTransparencyGD($rgba)
    {
        $alpha = ($rgba & 0x7F000000) >> 24;

        if ($alpha === 127)
            return true;

        return false;
    }

    /**
     * Get closest matching color
     * @param $r
     * @param $g
     * @param $b
     * @return mixed
     */
    public function getClosestColor($r, $g, $b)
    {
        $key = '';
        foreach ($this->RGBWhiteList as $value) {

            // Push difference into diffArray
            $diffArray[] = $this->getSimpleColorDiff($r, $value[0], $g, $value[1], $b, $value[2]);

            // Find the Lowest value in the Difference Array
            $smallest = min($diffArray);

            // Search for the lowest value and set the key variable to it
            $key = array_search($smallest, $diffArray);
        }

        // Return the hex array counterpart value
        return $this->hexWhiteList[$key];
    }

    /**
     * Simple Color Difference Calculation
     * @todo Move to ColorCalculations
     * @param $r
     * @param $rVal
     * @param $g
     * @param $gVal
     * @param $b
     * @param $bVal
     * @return float
     */
    private function getSimpleColorDiff($r, $rVal, $g, $gVal, $b, $bVal)
    {
        return sqrt(
            pow($r - $rVal, 2)
            +
            pow($g - $gVal, 2)
            +
            pow($b - $bVal, 2)
        );
    }

    /**
     * Get colors
     * @return array
     */
    public function getProminentColors()
    {
        // Count each color occurrence.
        $countEachColor = array_count_values($this->loadedImageColors);

        //unset transparent
        if (array_key_exists('transparent', $countEachColor))
            unset($countEachColor['transparent']);

        // Sort numerically
        asort($countEachColor, SORT_NUMERIC);

        // Reverse order, highest values first.
        $colors = array_reverse($countEachColor, true);

        $i = 0;
        $prominent = array();

        foreach ($colors as $hex => $count) {
            $prominent[] = $hex;
            $i++;
            if ($i >= $this->numColorsOnPalette) break;
        }

        return $prominent;
    }

    /**
     * RBG to XYZ
     * @param $rgb
     * @return array
     */
    protected function rgb_to_xyz($rgb)
    {
        $red = $rgb[0];
        $green = $rgb[1];
        $blue = $rgb[2];
        $_red = $red / 255;
        $_green = $green / 255;
        $_blue = $blue / 255;

        if ($_red > 0.04045) {
            $_red = ($_red + 0.055) / 1.055;
            $_red = pow($_red, 2.4);
        } else {
            $_red = $_red / 12.92;
        }

        if ($_green > 0.04045) {
            $_green = ($_green + 0.055) / 1.055;
            $_green = pow($_green, 2.4);
        } else {
            $_green = $_green / 12.92;
        }

        if ($_blue > 0.04045) {
            $_blue = ($_blue + 0.055) / 1.055;
            $_blue = pow($_blue, 2.4);
        } else {
            $_blue = $_blue / 12.92;
        }

        $_red *= 100;
        $_green *= 100;
        $_blue *= 100;
        $x = $_red * 0.4124 + $_green * 0.3576 + $_blue * 0.1805;
        $y = $_red * 0.2126 + $_green * 0.7152 + $_blue * 0.0722;
        $z = $_red * 0.0193 + $_green * 0.1192 + $_blue * 0.9505;
        return (array($x, $y, $z));
    }

    /**
     * XYZ to LAB
     * @param $xyz
     * @return array
     */
    protected function xyz_to_lab($xyz)
    {
        $x = $xyz[0];
        $y = $xyz[1];
        $z = $xyz[2];
        $_x = $x / 95.047;
        $_y = $y / 100;
        $_z = $z / 108.883;
        if ($_x > 0.008856) {
            $_x = pow($_x, 1 / 3);
        } else {
            $_x = 7.787 * $_x + 16 / 116;
        }
        if ($_y > 0.008856) {
            $_y = pow($_y, 1 / 3);
        } else {
            $_y = (7.787 * $_y) + (16 / 116);
        }
        if ($_z > 0.008856) {
            $_z = pow($_z, 1 / 3);
        } else {
            $_z = 7.787 * $_z + 16 / 116;
        }
        $l = 116 * $_y - 16;
        $a = 500 * ($_x - $_y);
        $b = 200 * ($_y - $_z);

        return (array('l' => $l, 'a' => $a, 'b' => $b));
    }
}