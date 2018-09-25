<?php

namespace StatonLab\TripalTestSuite\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use StatonLab\TripalTestSuite\Mocks\TestResponseMock;
use StatonLab\TripalTestSuite\Services\MenuCaller;
use StatonLab\TripalTestSuite\Services\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

trait MakesHTTPRequests
{
    /**
     * @var array
     */
    private $_cookies;

    /**
     * Send a GET request.
     *
     * @param string $uri
     * @param array $params
     * @param array $headers
     * @return TestResponse
     */
    public function get($uri, $params = [], $headers = [])
    {
        return $this->_call('GET', $uri, [
            'query' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * Sends a POST request.
     *
     * @param string $uri
     * @param array $params
     * @param array $headers
     * @return TestResponse
     */
    public function post($uri, $params = [], $headers = [])
    {
        return $this->_call('POST', $uri, [
            'form_params' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * Sends a DELETE request.
     *
     * @param string $uri
     * @param array $params
     * @param array $headers
     * @return \StatonLab\TripalTestSuite\Services\TestResponse
     */
    public function delete($uri, $params = [], $headers = [])
    {
        return $this->_call('DELETE', $uri, [
            'query' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * Sends a PUT request.
     *
     * @param string $uri
     * @param array $params
     * @param array $headers
     * @return \StatonLab\TripalTestSuite\Services\TestResponse
     */
    public function put($uri, $params = [], $headers = [])
    {
        return $this->_call('PUT', $uri, [
            'form_params' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * Sends a PATCH request.
     *
     * @param string $uri
     * @param array $params
     * @param array $headers
     * @return \StatonLab\TripalTestSuite\Services\TestResponse
     */
    public function patch($uri, $params = [], $headers = [])
    {
        return $this->_call('PATCH', $uri, [
            'form_params' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * Sends the request.
     *
     * @param string $method
     * @param string $uri
     * @params array $params
     *
     * @throws \Exception
     * @return TestResponse
     */
    private function _call($method, $uri, $params = [])
    {
        if (! str_begins_with('http', $uri)) {
            $client = new MenuCaller();

            return $client->setPath($uri)->setMethod($method)->addParams($params)->send();
        }

        try {
            $client = new Client();
            $uri = $this->_prepareURI($uri);

            if (! empty($this->_cookies)) {
                $domain = str_replace('http://', '', $GLOBALS['base_url']);
                $domain = str_replace('https://', '', $domain);
                $cookieJar = CookieJar::fromArray($this->_cookies, $domain);
                $params['cookies'] = $cookieJar;
            }

            return new TestResponse($client->request($method, $uri, $params));
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            PHPUnit::fail("Failed to send $method request to $uri. ".$exception->getMessage());
        }

        return null;
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

        if (str_begins_with($uri, '/') || ! str_begins_with($uri, 'http')) {
            return trim($base_url.'/'.trim($uri, '/'), '/');
        }

        return trim($uri, '/');
    }
}
