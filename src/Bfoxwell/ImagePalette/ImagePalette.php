<?php
/**
 * This file is part of the ImagePalette package.
 *
 * (c) Brian Foxwell <brian@foxwell.io>
 * (c) gandalfx - https://github.com/gandalfx
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
     * Colors Whitelist
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
    
    /**
     * Colors that were found to be prominent
     * Array of Color objects
     * 
     * @var array
     */
    protected $palette;
    
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
	 * @param null $overrideLib
	 */
    public function __construct($file, $precision = 10, $paletteLength = 5, $overrideLib = null)
    {
        $this->file = $file;
        $this->precision = $precision;
        $this->paletteLength = $paletteLength;
        
        // use provided libname or auto-detect
        $this->lib = $overrideLib ? $overrideLib : $this->detectLib();
        
        // create an array with color ints as keys
        $this->whiteList = array_fill_keys($this->whiteList, 0);
        
        // go!
        $this->process($this->lib);
        
        // sort whiteList
        arsort($this->whiteList);
        
        // sort whiteList accordingly
        $this->palette = array_map(
            function($color) {
                return new Color($color);
            },
            array_keys($this->whiteList)
        );
    }


	/**
	 * Autodetect and pick a graphical library to use for processing.
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
	 * @throws \Exception
	 * @return mixed
	 */
    protected function setWorkingImageGmagick()
    {
        throw new \Exception("Gmagick not supported");
    }
    
    /**
     * Get and set size of the image using GD.
     */
    protected function setImageSizeGD()
    {
        list($this->width, $this->height) = getimagesize($this->file);
    }
    
    /**
     * Get and set size of image using ImageMagick.
     */
    protected function setImageSizeImagick()
    {
        $d = $this->loadedImage->getImageGeometry();
        $this->width  = $d['width'];
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
                
                $color = $this->getPixelColor($x, $y);
                
                // transparent pixels don't really have a color
                if ($color->isTransparent())
                    continue 1;
                
                // increment closes whiteList color (key)
                $this->whiteList[ $this->getClosestColor($color) ]++;
            }
        }
    }
    
    /**
     * Get closest matching color
     * 
     * @param Color $color
     * @return int
     */
    protected function getClosestColor(Color $color)
    {
        
        $bestDiff = PHP_INT_MAX;
        
        // default to black so hhvm won't cry
        $bestColor = 0x000000;
        
        foreach ($this->whiteList as $wlColor => $hits) {
            
            // calculate difference (don't sqrt)
            $diff = $color->getDiff($wlColor);
            
            // see if we got a new best
            if ($diff < $bestDiff) {
                $bestDiff = $diff;
                $bestColor = $wlColor;
            }
        }
        
        return $bestColor;
    }
    
    /**
     * Returns an array describing the color at x,y
     * At index 0 is the color as a whole int (may include alpha)
     * At index 1 is the color's red value
     * At index 2 is the color's green value
     * At index 3 is the color's blue value
     * 
     * @param  int $x
     * @param  int $y
     * @return Color
     */
    protected function getPixelColor($x, $y)
    {
        return $this->{'getPixelColor' . $this->lib} ($x, $y);
    }
    
    /**
     * Using to retrieve color information about a specified pixel
     * 
     * @see  getPixelColor()
     * @param  int $x
     * @param  int $y
     * @return Color
     */
    protected function getPixelColorGD($x, $y)
    {
        $color = imagecolorat($this->loadedImage, $x, $y);
        // $rgb = imagecolorsforindex($this->loadedImage, $color);
        
        return new Color (
            $color
            // $rgb['red'],
            // $rgb['green'],
            // $rgb['blue']
        );
    }
    
    /**
     * Using to retrieve color information about a specified pixel
     * 
     * @see  getPixelColor()
     * @param  int $x
     * @param  int $y
     * @return Color
     */
    protected function getPixelColorImagick($x, $y)
    {
        $rgb = $this->loadedImage->getImagePixelColor($x, $y)->getColor();
        
        return new Color(array(
            $rgb['r'],
            $rgb['g'],
            $rgb['b'],
        ));
    }

    protected function getPixelColorGmagick($x, $y)
    {
        throw new \Exception("Gmagick not supported: ($x, $y)");
    }
    
    /**
     * Returns an array of Color objects
     * 
     * @param  int $paletteLength
     * @return array
     */
    public function getColors($paletteLength = null)
    {

        // allow custom length calls
        if (!is_numeric($paletteLength)) {
            $paletteLength = $this->paletteLength;
        }
        
        // take the best hits
        return array_slice($this->palette, 0, $paletteLength, true);
    }
    
    /**
     * Returns a json encoded version of the palette
     * 
     * @return string
     */
    public function __toString()
    {
        // Color PHP 5.3 compatible -> not JsonSerializable :(
        return json_encode(array_map(
            function($color) {
                return (string) $color;
            },
            $this->getColors()
        ));
    }

	/**
	 * Convenient getter access as properties
	 *
	 * @param $name
	 * @throws \Exception
	 * @return  mixed
	 */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        throw new \Exception("Method $method does not exist");
    }
    
    /**
     * Returns the palette for implementation of the IteratorAggregate interface
     * Used in foreach loops
     * 
     * @see  getColors()
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getColors());
    }
}
