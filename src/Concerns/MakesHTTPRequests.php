<?php

namespace StatonLab\TripalTestSuite\Concerns;

use GuzzleHttp\Client;
use StatonLab\TripalTestSuite\Services\TestResponse;

trait MakesHTTPRequests
{
    /**
     * Send a GET request.
     *
     * @param string $uri
     * @param array $params
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return TestResponse
     */
    public function get($uri, $params = [])
    {
        return $this->_call('GET', $uri, [
            'query' => $params,
        ]);
    }

    /**
     * Sends a POST request.
     *
     * @param string $uri
     * @param array $params
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return TestResponse
     */
    public function post($uri, $params = [])
    {
        return $this->_call('POST', $uri, [
            'form_params' => $params,
        ]);
    }

    /**
     * Sends the request.
     *
     * @param string $method
     * @param string $uri
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return TestResponse
     */
    private function _call($method, $uri, $params = [])
    {
        $client = new Client();

        $uri = $this->_prepareURI($uri);

        return new TestResponse($client->request($method, $uri, $params));
    }

    /**
     * Prepare url for request.
     *
     * @param $uri
     * @return string
     */
    private function _prepareURI($uri)
    {
        global $base_url;

        if (substr($uri, 0, 1) === '/') {
            return trim($base_url.$uri, '/');
        }

        if (! substr($uri, 0, 4) === 'http') {
            return trim($base_url.'/'.$uri, '/');
        }

        return trim($uri, '/');
    }
}
