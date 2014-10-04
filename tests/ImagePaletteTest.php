<?php
/**
 * This file is part of the ImagePalette package.
 *
 * (c) Brian McDonald <brian@brianmcdonald.io>
 * (c) gandalfx - https://github.com/gandalfx
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use BrianMcdo\ImagePalette\ImagePalette;

/**
 * Class ImagePaletteTest
 */
class ImagePaletteTest extends PHPUnit_Framework_Testcase
{
    private $palette;
	private $paletteObject;

    public function setUp() {
        $this->paletteObject = new ImagePalette(__DIR__.'/logo11w.png', 5, 20);
        $this->palette = $this->paletteObject->getColors();
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