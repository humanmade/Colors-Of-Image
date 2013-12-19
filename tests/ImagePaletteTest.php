<?php

class ImagePaletteTest extends PHPUnit_Framework_Testcase
{
    private $image;

    public function setUp() {
        $this->image = new \bfoxwell\ImagePalette\ImagePalette('https://www.google.com/images/srpr/logo11w.png');
    }

    public function tearDown() {
        $this->image = null;
    }

    public function testIntegrationImagePaletteIsObject()
    {
        return $this->assertTrue(is_object($this->image));
    }

    public function testIntegrationProminentColorsIsArray()
    {
        return $this->assertTrue(is_array($this->image->getProminentColors()));
    }
} 