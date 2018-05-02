<?php

namespace StatonLab\TripalTestSuite\Services;

use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\Assert as PHPUnit;
use SebastianBergmann\CodeCoverage\Report\PHP;

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
        $responseCode = $this->getStatusCode();

        PHPUnit::assertEquals($code, $responseCode,
            "Failed asserting that status code $code equals returned response code $responseCode");

        return $this;
    }

    /**
     * Assert the status code is successful.
     *
     * @return $this
     */
    public function assertSuccessful()
    {
        $responseCode = $this->getStatusCode();

        PHPUnit::assertTrue($responseCode >= 200 && $responseCode < 300,
            "Failed asserting that response status code $responseCode is a successful code.");

        return $this;
    }

    /**
     * Get the json response as an associative array.
     *
     * @return array
     */
    public function json()
    {
        $json = json_decode($this->getResponseBody(), true);
        if ($json === false) {
            PHPUnit::fail('Unable to decode json response!');
        }

        return $json;
    }

    /**
     * Assert that the response has the given structure.
     *
     * @param array $structure Expected structure
     * @params array $data Response data
     * @return $this
     */
    public function assertJsonStructure(array $structure, $data = null)
    {
        if (is_null($data)) {
            $data = $this->json();
        }

        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                PHPUnit::assertArrayHasKey($key, $data);

                $this->assertJsonStructure($structure[$key], $data[$key]);
                continue;
            }

            PHPUnit::assertArrayHasKey($value, $data);
        }

        return $this;
    }

    /**
     * Get response status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get response body.
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getResponseBody()
    {
        return $this->response->getBody();
    }

    /**
     * Get response headers.
     *
     * @return string[][]
     */
    public function getResponseHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * Get a specific header from the response.
     *
     * @param string $header
     * @return string[]
     */
    public function getResponseHeader($header)
    {
        return $this->response->getHeader($header);
    }

    /**
     * Assert string exists in response body.
     *
     * @param $content
     * @return $this
     */
    public function assertSee($content) {
        PHPUnit::assertContains($content, (string) $this->getResponseBody(), "Unable to find [$content] in response.");

        return $this;
    }
}
