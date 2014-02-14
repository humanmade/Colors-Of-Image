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

/**
 * Class Client
 * @package bfoxwell\ImagePalette
 */
class Client
{
    /**
     * Get most prominent colors as array
     * of Bfoxwell\ImagePalette\Color
     * 
     * @param mixed  $fileOrUrl
     * @param int    $precision
     * @param int    $maxNumColors
     * @param string $overrideExt
     * @return array
     */
    public function getColors($fileOrUrl, $precision = 10, $maxNumColors = 5, $overrideExt = null)
    {
        $load = new ImagePalette($fileOrUrl, $precision, $maxNumColors, $overrideExt);
        return $load->getColors();
    }
} 
