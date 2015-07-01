<?php

/*
 * Upload a file.
 */
Route::post(config('files.local_url'), function () {
    // TODO: validate the incoming file

    // Pull the file from the request
    $file = Input::file(config('files.input_name'));

    // Save the file to the remote server
    $remoteFile = FileApi::save($file);

    // Add the file to the local database
    $model = new \GridPrinciples\FileApi\Models\File;
    $model->fill([
        'file_name' => $remoteFile->file_name,
        'file_hash' => $remoteFile->hash,
        'file_size' => $remoteFile->file_size,
        'content_type' => $remoteFile->content_type,
    ])->save();

    // Return a response intended for JS uploaders
    return response()->json([
        'success' => 200,
        'filename' => $model->getUrl(),
        'hash' => $model->file_hash,
    ]);
});

/*
 * Serve a file (images as images, non-images as downloads).
 */
Route::get(config('files.local_url') . '/{file}/{size?}', function($file, $size = '') {
    return FileApi::serve($file->file_hash, $size);
});

/*
 * Delete a file.
 */
Route::delete(config('files.local_url') . '/{file}', function($file) {
    // Delete from CDN
    FileApi::delete($file->file_hash);

    // Delete from local DB
    $file->delete();

    return;
});
