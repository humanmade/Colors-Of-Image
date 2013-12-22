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

use \Bfoxwell\ImagePalette\ImagePalette;

/**
 * Class ImagePaletteTest
 */
class ImagePaletteTest extends PHPUnit_Framework_Testcase
{
    private $palette;

    public function setUp() {
        $this->paletteObject = new ImagePalette('https://www.google.com/images/srpr/logo11w.png', 5, 20);
        $this->palette = $this->paletteObject->getProminentColors();

    }

    public function tearDown() {
        $this->palette = null;
    }

    public function testIntegrationImagePaletteIsObject()
    {
        return $this->assertTrue(is_object($this->paletteObject));
    }

    public function testIntegrationProminentColorsIsArray()
    {
        return $this->assertTrue(is_array($this->palette));
    }

    public function testIfContainsBlue()
    {
        return $this->assertContains('#0066cc',$this->palette);
    }
} 