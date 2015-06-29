<?php

namespace GridPrinciples\FileApi\Facades;

use Illuminate\Support\Facades\Facade;

class FileApi extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'file_api';
    }

}
