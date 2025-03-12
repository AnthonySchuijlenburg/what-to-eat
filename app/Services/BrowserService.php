<?php

namespace App\Services;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpClient\HttpClient;

class BrowserService
{
    private HttpBrowser $browser;

    public function __construct()
    {
        $this->browser = new HttpBrowser(HttpClient::create());
    }

    /**
     * @param  string  $method  HTTP Method: GET, POST...
     * @param  string  $url  The url to make the request to
     */
    public function makeRequest(string $method, string $url): Response
    {
        $this->browser->request($method, $url);

        return $this->browser->getResponse();
    }
}
