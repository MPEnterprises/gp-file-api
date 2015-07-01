<?php

return [
    // Which file server will be interacted with
    'api_url'           => 'http://gp.file.server/api/v1/',

    // POST name of uploaded data
    'input_name'        => 'files',

    // Size (in KB) used when validating uploads
    'max_upload_size'   => 1024 * 4,

    // The database table name used for storing images locally
    'table_name'        => 'files',

    // Local application's endpoint for managing files
    'local_url'       => '/files',
];
