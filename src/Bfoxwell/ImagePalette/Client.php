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

/**
 * Class Client
 * @package bfoxwell\ImagePalette
 */
class Client
{
    /**
     * Get most prominent colors as array of ints
     * 
     * @param $fileOrUrl
     * @param int $precision
     * @param int $maxNumColors
     * @param $truePer
     * @return array
     */
    public function getColors($fileOrUrl, $precision = 10, $maxNumColors = 5, $overrideExt = null)
    {
        $load = new ImagePalette($fileOrUrl, $precision, $maxNumColors, $overrideExt);
        return $load->getColors();
    }
    
    /**
     * Get most prominent colors as array of array of ints (r,g,b)
     * 
     * @param $fileOrUrl
     * @param int $precision
     * @param int $maxNumColors
     * @param $truePer
     * @return array
     */
    public function getRgbColors($fileOrUrl, $precision = 10, $maxNumColors = 5, $overrideExt = null)
    {
        $load = new ImagePalette($fileOrUrl, $precision, $maxNumColors, $overrideExt);
        return $load->getRgbColors();
    }
    
    /**
     * Get most prominent colors as array of hex strings like '#ffffff'
     * 
     * @param $fileOrUrl
     * @param int $precision
     * @param int $maxNumColors
     * @param $truePer
     * @return array
     */
    public function getHexStringColors($fileOrUrl, $precision = 10, $maxNumColors = 5, $overrideExt = null)
    {
        $load = new ImagePalette($fileOrUrl, $precision, $maxNumColors, $overrideExt);
        return $load->getHexStringColors();
    }
    
    /**
     * Get most prominent colors as array of rgb strings like 'rgb(255,0,15)'
     * 
     * @param $fileOrUrl
     * @param int $precision
     * @param int $maxNumColors
     * @param $truePer
     * @return array
     */
    public function getRgbStringColors($fileOrUrl, $precision = 10, $maxNumColors = 5, $overrideExt = null)
    {
        $load = new ImagePalette($fileOrUrl, $precision, $maxNumColors, $overrideExt);
        return $load->getRgbStringColors();
    }
} 
