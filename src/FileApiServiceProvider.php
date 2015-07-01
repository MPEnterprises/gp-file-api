<?php

namespace GridPrinciples\FileApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FileApiServiceProvider extends ServiceProvider {

    public function boot()
    {
        // publish the config file
        $this->publishes([
            __DIR__.'/../resources/config.php' => config_path('files.php'),
        ]);

        // publish the migration
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('/migrations')
        ], 'migrations');

        // publish the view files
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'file');

        // bind the File model to a route keyword
        Route::bind('file', function($value)
        {
            if(!$found = File::where('file_hash', $value)->first())
            {
                return abort(404);
            }

            return $found;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // merge the config file with the user's
        $this->mergeConfigFrom(
            __DIR__.'/../config/files.php', config_path('files.php')
        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/services.php', config_path('services.php')
        );

        // bind the API to the container
        $this->app->bind('file_api', function()
        {
            return new \GridPrinciples\FileApi\Api;
        });
    }
}
