<?php

namespace GridPrinciples\FileApi;

use GridPrinciples\FileApi\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class FileApiServiceProvider extends ServiceProvider {

    public function boot()
    {
        // publish the config file
        $this->publishes([
            __DIR__.'/../config/files.php' => config_path('files.php'),
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

        // Add our custom file validation
        Validator::extend('uploaded_file', function($attribute, $value, $parameters) {
            $valid = DB::table(with(new File)->getTable())->where('file_hash', $value)->first();
            return (bool) $valid;
        });

        // bind the routes
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../resources/routes.php';
        }
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

        // bind the API to the container
        $this->app->bind('file_api', function()
        {
            return new \GridPrinciples\FileApi\Api;
        });
    }
}
