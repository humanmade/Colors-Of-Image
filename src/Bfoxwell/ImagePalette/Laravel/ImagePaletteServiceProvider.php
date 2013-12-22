<?php
namespace Bfoxwell\ImagePalette\Laravel;

use Illuminate\Support\ServiceProvider;

use Bfoxwell\ImagePalette\Client;

class ImagePaletteServiceProvider extends ServiceProvider {
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
        $this->package('bfoxwell/image-palette');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['image-palette'] = $this->app->share(
            function ($app) {
                return new Client;
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('image-palette');
    }

} 