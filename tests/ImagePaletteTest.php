<?php
/**
 * This file is part of the ImagePalette package.
 *
 * (c) Brian Foxwell <brian@foxwell.io>
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
	private $paletteObject;

    public function setUp() {
        $this->paletteObject = new ImagePalette('https://www.google.com/images/srpr/logo11w.png', 5, 20);
        $this->palette = $this->paletteObject->getColors();
		$this->clientObject = new \Bfoxwell\ImagePalette\Client();

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
		var_dump($this->palette);
        return $this->assertContains('#0066cc',$this->palette);
    }

	public function testIfClientContainsBlue()
	{
		$data = $this->clientObject->getColors("https://www.google.com/images/srpr/logo11w.png");
		var_dump($data);
		return $this->assertContains('#0066cc', $data);
	}
} 