<?php

namespace GridPrinciples\FileApi;

use Illuminate\Support\Facades\Facade;

class FileApiFacade extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'file_api';
    }

}
