<?php
use \bfoxwell\ImagePalette\ImagePalette;

class ImagePaletteTest extends PHPUnit_Framework_Testcase
{
    private $image;

    public function setUp() {
        $this->image = new ImagePalette('https://www.google.com/images/srpr/logo11w.png', 5);
    }

    public function tearDown() {
        $this->image = null;
    }

    public function testIntegrationImagePaletteIsObject()
    {
        var_dump($this->image);
        return $this->assertTrue(is_object($this->image));
    }

    public function testIntegrationProminentColorsIsArray()
    {
        var_dump($this->image->getProminentColors());
        return $this->assertTrue(is_array($this->image->getProminentColors()));
    }
} 