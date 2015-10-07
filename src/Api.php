<?php

namespace GridPrinciples\FileApi;

use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Api {

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => trim(config('files.api_url'), '/') . '/',
        ]);

        $this->credentials = [
            config('files.api_key'),
            config('files.api_secret')
        ];
    }

    public function all()
    {
        $body = $this->getAllFiles();
        return new Collection(json_decode($body));
    }

    public function download($hash, $size = '')
    {
        $response = $this->getFile($hash, $size, true);

        if(class_basename($response) == 'RedirectResponse')
        {
            return $this->cacheAndRespondForContentDeliveryNetwork($hash, $size, $response);
        }

        $contentDisposition = str_replace('attachment; ', '', $response->getHeader('Content-Disposition'));

        return (new Response($response->getBody(), $response->getStatusCode()))
            ->header('Content-type', $response->getHeaderLine('Content-Type'))
            ->header('Content-disposition', $contentDisposition);
    }

    public function serve($hash, $size = '', $download = false)
    {
        $response = $this->getFile($hash, $size, $download);


        if(class_basename($response) == 'RedirectResponse')
        {
            return $this->cacheAndRespondForContentDeliveryNetwork($hash, $size, $response);
        }

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

    private function getFile($hash, $size = '', $download = false)
    {
        // TODO: validate hash for protection against XSS attacks
        $cacheKey = $this->getCacheKey($hash, $size);

        if (Cache::has($cacheKey)) {
            return redirect()->to(Cache::get($cacheKey));
        }

        $url = $size ? 'view/' . e($hash) . '/' . $size : ($download ? 'download/' : 'view/') . e($hash);
        $response = $this->getFromServer($url);

        if($decoded = json_decode($response->getBody()))
        {
            // This is a JSON response instead of a file.
            if(isset($decoded->redirect))
            {
                return redirect()->to($decoded->redirect);
            }
        }

        return $response;
    }

    public function delete($hash)
    {
        $response = $this->getFromServer('delete/' . e($hash));

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

    private function cacheAndRespondForContentDeliveryNetwork($hash, $size, $response)
    {
        $cacheUrl = $response->getTargetUrl();
        $key = $this->getCacheKey($hash, $size);

        Cache::forever($key, $cacheUrl);

        return $response;
    }

    private function getCacheKey($hash, $size)
    {
        return implode(':', array_filter([$hash, $size]));
    }
}
