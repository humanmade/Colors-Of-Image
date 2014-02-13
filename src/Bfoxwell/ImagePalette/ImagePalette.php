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
use Imagick;

/**
 * Class ImagePalette
 *
 * Gets the prominent colors in a given image. To get common color matching, all pixels are matched
 * against a white-listed color palette.
 *
 * @package bfoxwell\ImagePalette
 */
class ImagePalette implements \IteratorAggregate
{
    /**
     * File or Url
     * @var string
     */
    protected $file;

    /**
     * Loaded Image
     * @var object
     */
    protected $loadedImage;

    /**
     * Loaded Image Colors in Hex
     * @var array
     */
    protected $loadedImageColors = array();

    /**
     * Process every Nth pixel
     * @var int
     */
    protected $precision;

    /**
     * Width of image
     * @var integer
     */
    protected $width;

    /**
     * Height of image
     * @var integer
     */
    protected $height;

    /**
     * Number of colors to return
     * @var integer
     */
    protected $paletteLength;

    /**
     * Hex Whitelist
     * @var array
     */
    protected $whiteList = array(
        0x660000, 0x990000, 0xcc0000, 0xcc3333, 0xea4c88, 0x993399,
        0x663399, 0x333399, 0x0066cc, 0x0099cc, 0x66cccc, 0x77cc33,
        0x669900, 0x336600, 0x666600, 0x999900, 0xcccc33, 0xffff00,
        0xffcc33, 0xff9900, 0xff6600, 0xcc6633, 0x996633, 0x663300,
        0x000000, 0x999999, 0xcccccc, 0xffffff, 0xE7D8B1, 0xFDADC7,
        0x424153, 0xABBCDA, 0xF5DD01
    );
    
    protected $whiteListHits;
    
    /**
     * Library used
     * Supported are GD and Imagick
     * @var string
     */
    protected $lib;

    /**
     * Constructor
     * @param string $file
     * @param int $precision
     * @param int $paletteLength
     */
    public function __construct($file, $precision = 10, $paletteLength = 5, $overrideLib = null)
    {
        $this->file = $file;
        
        $this->precision = $precision;
        $this->paletteLength = $paletteLength;
        
        $this->initWhiteList();
        
        if ($overrideLib) $this->lib = $overrideLib;
        else              $this->lib = $this->detectLib();
        
        $this->process($this->lib);
        
        arsort($this->whiteListHits);
    }


    /**
     * Autodetect and pick a graphical library to use for processing.
     * @param $lib
     * @return string
     */
    protected function detectLib()
    {
        try {
            if (extension_loaded('gd') && function_exists('gd_info')) {

                return 'GD';

            } else if(extension_loaded('imagick')) {

                return 'Imagick';

            } else if(extension_loaded('gmagick')) {

                return 'Gmagick';

            }

            throw new \Exception(
                "Try installing one of the following graphic libraries php5-gd, php5-imagick, php5-gmagick.
            ");

        } catch(\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
    
    protected function initWhiteList()
    {
        foreach($this->whiteList as $color) {
            $this->whiteListHits[$color] = 0;
        }
    }

    /**
     * Select a graphical library and start generating the Image Palette
     * @param string $lib
     * @throws \Exception
     */
    protected function process($lib)
    {
        try {
            
            $this->{'setWorkingImage' . $lib} ();
            $this->{'setImagesize' . $lib} ();
            
            $this->readPixels();
            
        } catch(\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
    
    /**
     * Load and set the working image.
     * @param $image
     * @param string $image
     */
    protected function setWorkingImageGD()
    {
        $extension = pathinfo($this->file, PATHINFO_EXTENSION);
        try {

            switch (strtolower($extension)) {
                case "png":
                    $this->loadedImage = imagecreatefrompng($this->file);
                    break;
                    
                case "jpg":
                case "jpeg":
                    $this->loadedImage = imagecreatefromjpeg($this->file);
                    break;
                    
                case "gif":
                    $this->loadedImage = imagecreatefromgif($this->file);
                    break;
                    
                case "bmp":
                    $this->loadedImage = imagecreatefrombmp($this->file);
                    break;
                    
                default:
                    throw new UnsupportedFileTypeException("The file type .$extension is not supported.");
            }

        } catch (UnsupportedFileTypeException $e) {
            echo $e->getMessage() . "\n";
        }
    }
    
    /**
     * Load and set working image
     *
     * @todo needs work
     * @param $image
     * @param string $image
     * @return mixed
     */
    protected function setWorkingImageImagick()
    {

        $file = file_get_contents($this->file);
        $temp = tempnam("/tmp", uniqid("ImagePalette_",true));
        file_put_contents($temp, $file);

        $this->loadedImage = new Imagick($temp);
    }
    
    /**
     * Load and set working image
     *
     * @todo needs work
     * @param $image
     * @param string $image
     * @return mixed
     */
    protected function setWorkingImageGmagick()
    {
        throw new \Exception("Gmagick not supported");
        return null;
    }
    
    /**
     * Get and set size of the image using GD.
     */
    protected function setImageSizeGD()
    {
        $dimensions = getimagesize($this->file);
        $this->width = $dimensions[0];
        $this->height = $dimensions[1];
    }
    
    /**
     * Get and set size of image using ImageMagick.
     */
    protected function setImageSizeImagick()
    {
        $d = $this->loadedImage->getImageGeometry();
        $this->width = $d['width'];
        $this->height = $d['height'];
    }
    
    /**
     * For each interesting pixel, add its closest color to the loaded colors array
     * 
     * @return mixed
     */
    protected function readPixels()
    {
        // Row
        for ($x = 0; $x < $this->width; $x += $this->precision) {
            // Column
            for ($y = 0; $y < $this->height; $y += $this->precision) {
                
                list($r, $g, $b) = $this->getPixelColor($x, $y);
                
                $this->whiteListHits[ $this->getClosestColor($r, $g, $b) ]++;
            }
        }
    }
    
    /**
     * Returns an array describing the color at x,y
     * At index 0 is the color's red value
     * At index 1 is the color's green value
     * At index 2 is the color's blue value
     * 
     * @param  int $x
     * @param  int $y
     * @return array
     */
    protected function getPixelColor($x, $y)
    {
        return $this->{'getPixelColor' . $this->lib} ($x, $y);
    }
    
    /**
     * Using  to retrive color information about a specified pixel
     * 
     * @see  getPixelColor()
     * @param  int $x
     * @param  int $y
     * @return array
     */
    protected function getPixelColorGD($x, $y)
    {
        $color = imagecolorat($this->loadedImage, $x, $y);
        $rgb = imagecolorsforindex($this->loadedImage, $color);
        
        return array(
            // $color,
            $rgb['red'],
            $rgb['green'],
            $rgb['blue']
        );
    }
    
    /**
     * Using  to retrive color information about a specified pixel
     * 
     * @see  getPixelColor()
     * @param  int $x
     * @param  int $y
     * @return array
     */
    protected function getPixelColorImagick($x, $y)
    {
        $rgb = $this->loadedImage->getImagePixelColor($x,$y)->getColor();
        
        return array(
            // $this->rgbToColor($rgb['r'], $rgb['g'], $rgb['b']),
            $rgb['r'],
            $rgb['g'],
            $rgb['b']
        );
    }

    protected function getPixelColorGmagick($x, $y)
    {
        throw new \Exception("Gmagick not supported");
        return;
    }

    /**
     * Detect Transparency using GD
     * @param $rgbaColor
     * @return bool
     */
    public function detectTransparency($rgbaColor)
    {
        $alpha = $rgbaColor >> 24;
        
        return $alpha === 127;
    }

    /**
     * Get closest matching color
     * 
     * @param $r
     * @param $g
     * @param $b
     * @return mixed
     */
    protected function getClosestColor($r, $g, $b)
    {
        
        $bestKey = 0;
        $bestDiff = PHP_INT_MAX;
        $whiteListLength = count($this->whiteList);
        
        for ( $i = 0 ; $i < $whiteListLength ; $i++ ) {
            
            // get whitelisted values
            list($wlr, $wlg, $wlb) = self::colorToRgb($this->whiteList[$i]);
            
            // calculate difference (don't sqrt)
            $diff = pow($r - $wlr, 2) + pow($g - $wlg, 2) + pow($b - $wlb, 2);
            
            // see if we got a new best
            if ($diff < $bestDiff) {
                $bestDiff = $diff;
                $bestKey = $i;
            }
        }
        
        return $this->whiteList[$bestKey];
    }
    
    /**
     * Returns an array containing int values for
     * red, green and blue
     * 
     * @param  ing $color
     * @return array
     */
    public static function colorToRgb($color)
    {
        return array(
            
            // red
            ($color / 0x10000) % 0x100,
            
            // green
            ($color / 0x100) % 0x100,
            
            // blue
            $color % 0x100
        );
    }
    
    /**
     * Returns an int representing the color
     * defined by the red, green and blue values
     * 
     * @param  int $r
     * @param  int $g
     * @param  int $b
     * @return int
     */
    public static function rgbToColor($r, $g, $b)
    {
        return $r * 0x10000 + $g * 0x100 + $b;
    }

    /**
     * Get colors
     * @return array
     */
    public function getColors()
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
            if ($i >= $this->paletteLength) break;
        }

        return $prominent;
    }
    
    /**
     * Returns the color palette as an array containing
     * an integer for each color
     * 
     * @param  int $paletteLength
     * @return array
     */
    public function getPalette($paletteLength = null)
    {
        // allow custom length calls
        if (!is_numeric($paletteLength)) {
            $paletteLength = $this->paletteLength;
        }
        
        // keys of hits array are colors as int
        return array_keys(
            // take the best hits, preserve keys
            array_slice($this->whiteListHits, 0, $paletteLength, true)
        );
    }
    
    /**
     * Returns the color palette as an array containing
     * each color as an array of red, green and blue values
     * 
     * @param  int $paletteLength
     * @return array
     */
    public function getRgbArraysPalette($paletteLength = null)
    {
        return array_map(
            // static method call
            array('self', 'colorToRgb'),
            $this->getPalette($paletteLength)
        );
    }
    
    /**
     * Returns the color palette as an array containing
     * hexadecimal string representations, like '#abcdef'
     * 
     * @param  int $paletteLength
     * @return array
     */
    public function getHexStringPalette($paletteLength = null)
    {
        return array_map(function($color) {
                return '' . str_pad(dechex($color), 6, '0', STR_PAD_LEFT);
            },
            $this->getPalette($paletteLength)
        );
    }
    
    /**
     * Returns the color palette as an array containing
     * decimal string representations, like 'rgb(123,0,20)'
     * 
     * @param  int $paletteLength
     * @return array
     */
    public function getRgbStringPalette($paletteLength = null)
    {
        return array_map(function($rgb) {
                return 'rgb(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ')';
            },
            $this->getRgbArraysPalette($paletteLength)
        );
    }
    
    /**
     * Returns a json encoded version of the palette
     * 
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->getHexStringPalette());
    }
    
    /**
     * Returns the palette for implementation of the IteratorAggregate interface
     * Used in foreach loops
     * 
     * @see  getPalette()
     * @return array
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getPalette());
    }
}
