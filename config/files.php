<?php

return [
    // Which file server will be interacted with
    'api_url'           => 'https://files.gridprinciples.com/api/v1/',

    // API Credentials
    'api_key'           => env('FILE_API_KEY'),
    'api_secret'        => env('FILE_API_SECRET'),

    // POST name of uploaded data
    'input_name'        => 'files',

    // Size (in MB) used when validating uploads
    'max_upload_size'   => 4,

    // The database table name used for storing images locally
    'table_name'        => 'files',

    // Local application's endpoint for managing files
    'local_url'       => '/files',
];
