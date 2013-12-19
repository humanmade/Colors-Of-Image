<?php

class ImagePaletteTest extends PHPUnit_Framework_Testcase
{
    public function testImagePaletteIsArrayIntegration()
    {
        $image = new \bfoxwell\ImagePalette\ImagePalette('https://www.google.com/images/srpr/logo11w.png');
        return $this->assertTrue($image);
    }
} 