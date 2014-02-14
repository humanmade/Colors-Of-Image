<?php
/**
 * Collection of static color converters
 * 
 * @author  Gandalx
 */

namespace Bfoxwell\ImagePalette;

/**
 * Class ColorUtil
 * 
 * @package bfoxwell\ImagePalette
 */
class ColorUtil
{
    /**
     * Detect Transparency using GD
     * Returns true if the provided color has zero opacity
     * 
     * @param $rgbaColor
     * @return bool
     */
    public static function isTransparent($rgbaColor)
    {
        $alpha = $rgbaColor >> 24;
        return $alpha === 127;
    }
    
    /**
     * Returns an array containing int values for
     * red, green and blue
     * 
     * @param  ing $color
     * @return array
     */
    public static function intToRgb($color)
    {
        return array(
            
            // red
            ($color >> 16) & 0xff,
            
            // green
            ($color >> 8) & 0xff,
            
            // blue
            $color & 0xff
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
    public static function rgbToInt($r, $g, $b)
    {
        return ($r << 16) | ($g << 8) | $b;
    }
    
    /**
     * Render 6-digit hexadecimal string representation
     * like '#abcdef'
     * 
     * @param  int $color
     * @return string
     */
    public static function intToHexString($color)
    {
        return '#' . str_pad(dechex($color), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Render 3-integer decimal string representation
     * like 'rgb(123,0,20)'
     * 
     * @param  array $rgb  array of three ints or decimal string representations
     * @return string
     */
    public static function rgbToString($rgb)
    {
        return 'rgb(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ')';
    }
}
