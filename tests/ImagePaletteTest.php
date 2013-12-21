<?php

/**
 * This file is part of the ImagePalette package.
 *
 * (c) Joe Hoyle <joe@hmn.md>
 * (c) Brian Foxwell <brian@foxwell.io>
 * (c) Marc Pacheco <Marckk1997@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use \bfoxwell\ImagePalette\ImagePalette;

/**
 * Class ImagePaletteTest
 */
class ImagePaletteTest extends PHPUnit_Framework_Testcase
{
    private $palette;

    public function setUp() {
        $this->palette = new ImagePalette('https://www.google.com/images/srpr/logo11w.png', 5);
    }

    public function tearDown() {
        $this->palette = null;
    }

    public function testIntegrationImagePaletteIsObject()
    {
        return $this->assertTrue(is_object($this->palette));
    }

    public function testIntegrationProminentColorsIsArray()
    {
        return $this->assertTrue(is_array($this->palette->getProminentColors()));
    }

    /**
     * Return an array map of all colors to their matching color counter part.
     * @return mixed
     */
    public function testColorMapContainsWhite()
    {
        $width = $this->palette->width;
        $height= $this->palette->height;
        $hexArray = array();

        for( $x = 0; $x < $width; $x += $this->palette->precision ) {
            for ( $y = 0; $y < $height; $y += $this->palette->precision ) {

                $index = imagecolorat($this->palette->workingImage, $x, $y);
                $rgb = imagecolorsforindex($this->palette->workingImage, $index);

                $color = $this->palette->getClosestColor( $rgb["red"], $rgb["green"], $rgb["blue"] );

                $hexArray[ $this->palette->RGBToHex( $rgb["red"], $rgb["green"], $rgb["blue"] ) ] = $this->palette->RGBToHex( $color[0], $color[1], $color[2] );
            }
        }

        return $this->assertContains('#FFFFFF', $hexArray);
    }
} 