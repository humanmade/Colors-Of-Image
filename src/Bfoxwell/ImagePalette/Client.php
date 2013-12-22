<?php
/**
 * This file is part of the ImagePalette package.
 *
 * (c) Joe Hoyle <joe@hmn.md>
 * (c) Brian Foxwell <brian@foxwell.io>
 * (c) Marc Pacheco <Marckk1997@gmail.com>
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
     * Get most prominent colors.
     * @param $fileOrUrl
     * @param int $precision
     * @param int $maxNumColors
     * @param $truePer
     * @return array
     */
    public function getColors($fileOrUrl, $precision = 10, $maxNumColors = 5, $truePer = true)
    {
        $load = new ImagePalette($fileOrUrl, $precision = 10, $maxNumColors = 5, $truePer = true);
        return $load->getProminentColors();
    }
} 