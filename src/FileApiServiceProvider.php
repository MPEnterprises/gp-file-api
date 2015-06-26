<?php

namespace GridPrinciples\FileApi;

use Illuminate\Support\ServiceProvider;

class FileApiServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('file_api', function()
        {
            return new \GridPrinciples\FileApi\Api;
        });
    }
}
