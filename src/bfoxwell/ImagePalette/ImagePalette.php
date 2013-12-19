<?php
namespace bfoxwell\ImagePalette;
use Exception;

/**
 * Gets the prominent colors in a given image. To get common color matching, all pixels are matched
 * against a white-listed color palette.
 *
 * @author  Joe Hoyle joe@hmn.md
 *
 * Props to the following people who I ripped some of this code from:
 *
 * Marc Pacheco
 *
 */
class ImagePalette {

    public $image;
    public $height;
    public $width;
    public $precision;
    public $coinciditions;
    public $maxnumcolors;
    public $trueper;
    public $color_map = array();
    public $_palette = array();
    public $_min_percentage = 10;
    public $_excluded_colors = array( '#FFFFFF' );

    static $hit_pixels = 0;
    static $missed_pixels = 0;

    public function __construct( $image, $precision = 10, $maxnumcolors = 5, $trueper = true ) {
        $this->image = $image;
        $this->maxnumcolors = $maxnumcolors;
        $this->trueper = $trueper;
        $this->getImageSize();
        $this->precision = $precision;

        $this->readPixels();

        $this->_excluded_colors[] = $this->getBackgroundColor();
    }

    public static function helper()
    {
        return new HelperAPI;
    }

    public function setMinPercentage( $num ) {
        $this->_min_percentage = $num;
    }

    public function readPixels() {

        $image 		= $this->image;
        $width 		= $this->width;
        $height 	= $this->height;
        $arrayex 	= explode( '.', $image );
        $typeOfImage= end( $arrayex );

        try {
            switch ( $typeOfImage ) {
                case "png":
                    $outputimg = "imagecreatefrompng";
                    break;
                case "jpg":
                    $outputimg = "imagecreatefromjpeg";
                    break;
                case "gif":
                    $outputimg = "imagecreatefromgif";
                    break;
                case "bpm":
                    $outputimg = "imagecreatefrombmp";
                    break;
                default: return;
            }

            $this->workingImage = $outputimg($image);

        } catch (Exception $e) {
            echo $e->getMessage()."\n";
            exit();
        }

        for( $x = 0; $x < $width; $x += $this->precision ) {
            for ( $y = 0; $y < $height; $y += $this->precision ) {

                $index = imagecolorat($this->workingImage, $x, $y);
                $rgb = imagecolorsforindex($this->workingImage, $index);

                $color = $this->getClosestColor( $rgb["red"], $rgb["green"], $rgb["blue"] );

                $hexarray[] = $this->RGBToHex( $color[0], $color[1], $color[2] );
            }
        }

        $coinciditions = array_count_values($hexarray);
        $this->coinciditions = $coinciditions;

        return true;
    }

    public function getBackgroundColor( $use_palette = true ) {

        $top_left_color = imagecolorsforindex( $this->workingImage, imagecolorat( $this->workingImage, 0, 0) );
        $top_left = array( $top_left_color['red'], $top_left_color['green'], $top_left_color['blue'] );

        $top_right_color = imagecolorsforindex( $this->workingImage, imagecolorat( $this->workingImage, $this->width - 1, 0 ) );
        $top_right = array( $top_right_color['red'], $top_right_color['green'], $top_right_color['blue'] );

        $bottom_left_color = imagecolorsforindex( $this->workingImage, imagecolorat( $this->workingImage, 0, $this->height - 1 ) );
        $bottom_left = array( $bottom_left_color['red'], $bottom_left_color['green'], $bottom_left_color['blue'] );

        $bottom_right_color = imagecolorsforindex( $this->workingImage, imagecolorat( $this->workingImage, $this->width - 1, $this->height - 1 ) );
        $bottom_right = array( $bottom_right_color['red'], $bottom_right_color['green'], $bottom_right_color['blue'] );

        if ( $use_palette ) {
            $top_left 		= call_user_func_array( array( $this, 'getClosestColor' ), $top_left );
            $top_right 		= call_user_func_array( array( $this, 'getClosestColor' ), $top_right );
            $bottom_right	 	= call_user_func_array( array( $this, 'getClosestColor' ), $bottom_right );
            $bottom_left 		= call_user_func_array( array( $this, 'getClosestColor' ), $bottom_left );
        }

        $colors = array( $top_left, $top_right, $bottom_left, $bottom_right);

        if( count( array_unique( $colors[0] ) ) == 1 ) {
            return $this->RGBToHex( $top_left[0], $top_left[1], $top_left[2] );
        }

        return null;
    }

    static public function RGBToHex($r, $g, $b){

        $hex = "#";
        $hex.= str_pad( dechex($r), 2, "0", STR_PAD_LEFT );
        $hex.= str_pad( dechex($g), 2, "0", STR_PAD_LEFT );
        $hex.= str_pad( dechex($b), 2, "0", STR_PAD_LEFT );

        return strtoupper($hex);
    }

    static public function HexToRGB($hex) {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = array($r, $g, $b);

        return $rgb; // returns an array with the rgb values
    }

    private function getPercentageOfColors(){

        $coinciditions = $this->coinciditions;

        $total = 0;

        foreach ($coinciditions as $color => $cuantity) {

            if ( in_array( $color, $this->_excluded_colors ) )
                unset( $coinciditions[$color] );

            else
                $total += $cuantity;
        }

        foreach ($coinciditions as $color => $cuantity) {
            $percentage = (($cuantity/$total)*100);
            $finallyarray["$color"] = $percentage;
        }

        if ( ! $coinciditions )
            return array();

        asort($finallyarray);
        array_keys($finallyarray);
        $outputarray = array_slice(array_reverse($finallyarray), 0, $this->maxnumcolors);

        $trueper = $this->trueper;

        if( $trueper && $outputarray ) {

            $total = 0;
            foreach ($outputarray as $color => $cuantity) {
                $total += $cuantity;
            }
            foreach ($outputarray as $color => $cuantity) {
                $percentage = (($cuantity/$total)*100);
                $finallyarrayp["$color"] = $percentage;
            }
            return $finallyarrayp;

        } else {

            return $outputarray;
        }
    }

    public function getImageSize() {

        $imgsize 	= getimagesize($this->image);
        $height 	= $imgsize[1];
        $width 		= $imgsize[0];
        $this->height = $height;
        $this->width = $width;

        return "x= ".$width."y= ".$height;
    }

    public function getProminentColors() {

        $pixels 		= $this->getPercentageOfColors();


        foreach ($pixels as $color => $value) {
            if ( $value < $this->_min_percentage )
                unset( $pixels[$color] );
        }

        $_c = array();

        foreach ($pixels as $key => $value) {
            $_c[] = $key;
        }

        return $_c;
    }

    public function getClosestColor($r, $g, $b){

        if ( isset( $this->color_map[$this->RGBToHex( $r, $g, $b ) ] ) ) {
            return $this->color_map[$this->RGBToHex( $r, $g, $b )];
        }

        $differencearray = array();
        $colors = $this->getPalette();

        foreach ($colors as $key => $value) {
            $value = $value['rgb'];
            $differencearray[$key] = self::getDistanceBetweenColors( $value, array( $r, $g, $b ) );
        }

        $smallest = min( $differencearray );

        $key = array_search($smallest, $differencearray);

        $color = $this->color_map[$this->RGBToHex( $r, $g, $b )] = $colors[$key]['rgb'];

        return $color;
    }

    private static function getDistanceBetweenColors( $col1, $col2 ) {

        $xyz1 = self::rgb_to_xyz( $col1 );
        $xyz2 = self::rgb_to_xyz( $col2 );

        $lab1 = self::xyz_to_lab( $xyz1 );
        $lab2 = self::xyz_to_lab( $xyz2 );

        return self::helper()->ciede2000( $lab1, $lab2 );
    }

    private function getPalette() {

        if ( ! empty( $this->_palette ) )
            return $this->_palette;

        $str = '["#660000", "#990000", "#cc0000", "#cc3333", "#ea4c88", "#993399", "#663399", "#333399", "#0066cc", "#0099cc", "#66cccc", "#77cc33", "#669900", "#336600", "#666600", "#999900", "#cccc33", "#ffff00", "#ffcc33", "#ff9900", "#ff6600", "#cc6633", "#996633", "#663300", "#000000", "#999999", "#cccccc", "#ffffff", "#E7D8B1", "#FDADC7", "#424153", "#ABBCDA", "#F5DD01"]';

        $hexs = json_decode( $str );

        foreach ( $hexs as $hex )
            $this->_palette[] = array( 'rgb' => self::HexToRGB( $hex ), 'hex' => $hex );

        return $this->_palette;
    }

    private static function xyz_to_lab( $xyz ){
        $x = $xyz[0];
        $y = $xyz[1];
        $z = $xyz[2];
        $_x = $x/95.047;
        $_y = $y/100;
        $_z = $z/108.883;
        if($_x>0.008856){
            $_x = pow($_x,1/3);
        }
        else{
            $_x = 7.787*$_x + 16/116;
        }
        if($_y>0.008856){
            $_y = pow($_y,1/3);
        }
        else{
            $_y = (7.787*$_y) + (16/116);
        }
        if($_z>0.008856){
            $_z = pow($_z,1/3);
        }
        else{
            $_z = 7.787*$_z + 16/116;
        }
        $l= 116*$_y -16;
        $a= 500*($_x-$_y);
        $b= 200*($_y-$_z);

        return(array( 'l' => $l, 'a' => $a, 'b' => $b));
    }


    private static function rgb_to_xyz( $rgb ) {
        $red = $rgb[0];
        $green = $rgb[1];
        $blue = $rgb[2];
        $_red = $red/255;
        $_green = $green/255;
        $_blue = $blue/255;

        if ( $_red > 0.04045 ) {
            $_red = ($_red+0.055)/1.055;
            $_red = pow($_red,2.4);
        } else{
            $_red = $_red/12.92;
        }

        if ( $_green > 0.04045 ) {
            $_green = ($_green+0.055)/1.055;
            $_green = pow($_green,2.4);
        } else{
            $_green = $_green/12.92;
        }

        if ( $_blue>0.04045 ) {
            $_blue = ($_blue+0.055)/1.055;
            $_blue = pow($_blue,2.4);
        } else {
            $_blue = $_blue/12.92;
        }

        $_red *= 100;
        $_green *= 100;
        $_blue *= 100;
        $x = $_red * 0.4124 + $_green * 0.3576 + $_blue * 0.1805;
        $y = $_red * 0.2126 + $_green * 0.7152 + $_blue * 0.0722;
        $z = $_red * 0.0193 + $_green * 0.1192 + $_blue * 0.9505;
        return(array( $x, $y, $z ));
    }

}