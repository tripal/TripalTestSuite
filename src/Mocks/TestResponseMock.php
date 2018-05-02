<?php
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

        if (! isset($response['status'])) {
            $this->response['status'] = 200;
        }
    }

    public function json()
    {
        return json_decode($this->response['body'], true);
    }

    public function getStatusCode()
    {
        return $this->response['status'];
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
