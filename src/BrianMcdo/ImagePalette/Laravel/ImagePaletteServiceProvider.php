<?php
/**
 * This file is part of the ImagePalette package.
 *
 * (c) Brian McDonald <brian@brianmcdonald.io>
 * (c) gandalfx - https://github.com/gandalfx
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrianMcdo\ImagePalette\Laravel;

use Illuminate\Support\ServiceProvider;
use BrianMcdo\ImagePalette\Client;

class ImagePaletteServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('brianmcdo/image-palette');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['image-palette'] = $this->app->share(function()
        {
            return new Client;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return array('image-palette');
    }

} 