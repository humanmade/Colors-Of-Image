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
        return $rgbaColor >> 24 === 127;
    }
    
    /**
     * Expands short notation colors
     * like 0xabc to long notation 0xaabbcc
     * 
     * @param  int $shortColor
     * @return int
     */
    public static function expand($shortColor)
    {
        // check if we really got a short color
        if ($shortColor >> 12 !== 0) return $shortColor;
        
        list($r, $g, $b) = self::shortToRgb($shortColor);
        return self::rgbToInt($r, $g, $b);
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
     * Returns an array containing int values for
     * red, green and blue of a color in short
     * notation like 0xabc
     * 
     * @param  ing $color
     * @return array
     */
    public static function shortToRgb($shortColor)
    {
        return array(
            
            // red
            (($shortColor >> 8) & 0xf) * 0x11,
            
            // green
            (($shortColor >> 4) & 0xf) * 0x11,
            
            // blue
            ($shortColor & 0xf) * 0x11
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
