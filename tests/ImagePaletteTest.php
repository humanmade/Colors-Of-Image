<?php

class ImagePaletteTest extends PHPUnit_Framework_Testcase
{
    private $image;

    public function setUp() {
        $this->image = new \bfoxwell\ImagePalette\ImagePalette('http://wallpapers.wallbase.cc/high-resolution/wallpaper-3033045.jpg');
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
        var_dump($this->image->getProminentColors());
        return $this->assertTrue(is_array($this->image->getProminentColors()));
    }
} 