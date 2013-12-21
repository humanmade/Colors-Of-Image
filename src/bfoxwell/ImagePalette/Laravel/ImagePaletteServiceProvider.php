<?php
namespace bfoxwell\ImagePalette\Laravel;

use Illuminate\Support\ServiceProvider;

use bfoxwell\ImagePalette\Client;

class ImagePaletteServiceProvider {
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
        $this->app['bfoxwell/image-palette'] = $this->app->share(
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
        return array('bfoxwell/image-palette');
    }

} 