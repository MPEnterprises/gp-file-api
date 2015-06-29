<?php

namespace GridPrinciples\FileApi;

use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class Api {

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('files.api_url') . 'v' . config('files.api_version') . '/',
        ]);
        $this->credentials = [
            config('services.files.key'), config('services.files.secret')
        ];
    }

    public function all()
    {
        $body = $this->getAllFiles();
        return new Collection(json_decode($body));
    }

    public function serve($hash, $size = '')
    {
        $response = $this->getFile($hash, $size);
        $contentDisposition = str_replace('attachment; ', '', $response->getHeader('Content-Disposition'));

        return (new Response($response->getBody(), $response->getStatusCode()))
            ->header('Content-type', $response->getHeaderLine('Content-Type'))
            ->header('Content-disposition', $contentDisposition);
    }

    public function save($file)
    {
        $body = fopen($file, 'r');
        $response = $this->postToServer('upload', $body, $file->getClientOriginalName());

        return json_decode($response->getBody());
    }

    private function getAllFiles()
    {
        return $this->getFromServer('all')->getBody();
    }

    private function getFile($hash, $size = '')
    {
        // TODO: validate hash for protection against XSS attacks
        $response = $this->getFromServer($size ? 'view/' . e($hash) . '/' . $size : 'view/' . e($hash));

        return $response;
    }

    private function getFromServer($url)
    {
        $response = $this->client->get($url, ['auth' => $this->credentials]);

        return $response;
    }

    private function postToServer($url, $body, $filename)
    {
        $response = $this->client->post($url, ['auth' => $this->credentials, 'multipart' => [
        [
            'name'     => 'file',
            'contents' => $body,
            'filename' => $filename,
        ]]]);

        return $response;
    }
}
