<?php

class Color {
    
    /**
     * Red value
     * @var int
     */
    public $r;
    
    /**
     * Green value
     * @var int
     */
    public $g;
    
    /**
     * Blue value
     * @var int
     */
    public $b;
    
    /**
     * Alpha value
     * @var int
     */
    public $a;
    
    
    /**
     * Construct new Color
     * 
     * @param int $color
     */
    public function __construct($color = 0x000000, $short = false)
    {
        if (is_numeric($color)) {
            
            if ($short) {
                $this->r = (($color >>  8) & 0xf) * 0x11;
                $this->g = (($color >>  4) & 0xf) * 0x11;
                $this->b =  ($color        & 0xf) * 0x11;
                $this->a =  ($color >> 12)        * 0x11;
            } else {
                $this->r = ($color >> 16) & 0xf;
                $this->g = ($color >>  8) & 0xf;
                $this->b =  $color        & 0xf;
                $this->a =  $color >> 24;
            }
            
        } elseif (is_array($color)) {
            
            list($this->r, $this->g, $this->b) = $color;
            
            if (count($color) > 3)
                $this->a = $color[3];
        }
    }
    
    /**
     * Some useful magic getters
     * 
     * @param  string $property
     * @return mixed
     */
    public function __get($property)
    {
        $method = 'to' . ucfirst($property);
        if (method_exists($this, $method))
            return $this->$method();
        
        switch ($property) {
            case 'red':   case 'getRed':
                return $this->r;
            case 'green': case 'getGreen':
                return $this->g;
            case 'blue':  case 'getBlue':
                return $this->b;
            case 'alpha': case 'getAlpha':
                return $this->a;
        }
        
        throw new \Exception("Property $property does not exist");
    }
    
    /**
     * Magic method, alias for toHexString
     * 
     * @see  toHexString()
     * @return string [description]
     */
    public function __toString()
    {
        return $this->toHexString();
    }
    
    /**
     * Whether or not this color has an alpha value > 0
     * 
     * @see isTransparent for full transparency
     * @return boolean
     */
    public function hasAlpha()
    {
        return (boolean) $this->a;
    }
    
    /**
     * Detect Transparency using GD
     * Returns true if the provided color has zero opacity
     * 
     * @param $rgbaColor
     * @return bool
     */
    public function isTransparent()
    {
        return $this->a === 127;
    }
    
    /**
     * Returns an array containing int values for
     * red, green and blue
     * 
     * @return array
     */
    public function toRgb()
    {
        return array($this->r, $this->g, $this->b);
    }
    
    /**
     * Returns an array containing int values for
     * red, green and blue and a double for alpha
     * 
     * @return array
     */
    public function toArgb()
    {
        return array($this->r, $this->g, $this->b, $this->a / 0x100);
    }
    
    /**
     * Returns an int representing the color
     * defined by the red, green and blue values
     * 
     * @return int
     */
    public function toInt()
    {
        return ($this->r << 16) | ($this->g << 8) | $this->b;
    }
    
    /**
     * Render 6-digit hexadecimal string representation
     * like '#abcdef'
     * 
     * @param  string  $prefix  defaults to '#'
     * @return string
     */
    public function toHexString($prefix = '#')
    {
        return $prefix . str_pad(dechex($this->toInt), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Render 3-integer decimal string representation
     * like 'rgb(123,0,20)'
     * 
     * @param  string  $prefix  defaults to 'rgb'
     * @return string
     */
    public function toRgbString($prefix = 'rgb')
    {
        return $prefix  . '('
             . $this->r . ','
             . $this->g . ','
             . $this->b . ')';
    }
    
    /**
     * Render 3-integer decimal string representation
     * like 'argb(123,0,20,0.5)'
     * 
     * @param  string  $prefix          defaults to 'argb'
     * @param  int     $alphaPrecision  max alpha digits, default 2
     * @return string
     */
    public function toArgbString($prefix = 'argb', $alphaPrecision = 2)
    {
        return $prefix  . '('
             . $this->r . ','
             . $this->g . ','
             . $this->b . ','
             . round($this->a / 0x100, $alphaPrecision) . ')';
    }
}
