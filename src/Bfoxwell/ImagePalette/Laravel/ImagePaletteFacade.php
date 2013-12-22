<?php
/**
 * This file is part of the ImagePalette package.
 *
 * (c) Brian Foxwell <brian@foxwell.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bfoxwell\ImagePalette\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * Class ImagePaletteFacade
 * @package bfoxwell\ImagePalette\Laravel
 */
class ImagePaletteFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'image-palette';
    }
} 