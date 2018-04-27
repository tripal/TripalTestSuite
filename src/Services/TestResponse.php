<?php

namespace StatonLab\TripalTestSuite\Services;

use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\Assert as PHPUnit;

class TestResponse
{
    /**
     * HTTP Response
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * TestResponse constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Assert status code.
     *
     * @param int $code
     * @return $this
     */
    public function assertStatus($code)
    {
        $responseCode = $this->response->getStatusCode();

        PHPUnit::assertEquals(
            $code,
            $responseCode,
            "Failed asserting that status code $code equals returned response code $responseCode"
        );

        return $this;
    }

    /**
     * Assert the status code is successful.
     *
     * @return $this
     */
    public function assertSuccessful() {
        $responseCode = $this->response->getStatusCode();

        PHPUnit::assertTrue(
            $responseCode >= 200 && $responseCode < 300,
            "Failed asserting that response status code $responseCode is a successful code."
        );

        return $this;
    }
}
