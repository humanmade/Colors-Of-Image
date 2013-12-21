<?php
namespace bfoxwell\ImagePalette\Laravel;

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