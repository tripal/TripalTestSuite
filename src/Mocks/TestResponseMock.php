<?php
/**
 * Created by PhpStorm.
 * User: Almsaeed
 * Date: 4/30/18
 * Time: 10:57 AM
 */

namespace StatonLab\TripalTestSuite\Mocks;

use StatonLab\TripalTestSuite\Services\TestResponse;

class TestResponseMock extends TestResponse
{
    protected $response;

    public function __construct($response = [])
    {
        $this->response = $response;

        if (isset($response['body']) && is_array($response['body'])) {
            $this->response['body'] = json_encode($response['body']);
        }
    }

    public function json()
    {
        return json_decode($this->response['body'], true);
    }

    public function getStatusCode()
    {
        return 200;
    }

    public function getResponseHeaders()
    {
        return $this->response['headers'];
    }

    public function getResponseHeader($header)
    {
        return $this->response['headers'][$header];
    }

    public function getResponseBody()
    {
        return $this->response['body'];
    }
}
