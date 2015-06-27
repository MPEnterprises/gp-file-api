<?php

namespace GridPrinciples\FileApi;

use Illuminate\Support\ServiceProvider;

class FileApiServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/config.php' => config_path('files.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../resources/config.php', config_path('files.php')
        );

        $this->app->bind('file_api', function()
        {
            return new \GridPrinciples\FileApi\Api;
        });
    }
}
