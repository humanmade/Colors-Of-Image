<?php
use \bfoxwell\ImagePalette\ImagePalette;

class ImagePaletteTest extends PHPUnit_Framework_Testcase
{
    public function testIntegrationOfImagePalette()
    {
        $image = new ImagePalette('https://www.google.com/images/srpr/logo11w.png');
        return $this->assertTrue($image);
    }
} 