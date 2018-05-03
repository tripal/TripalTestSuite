<?php

namespace StatonLab\TripalTestSuite\Services;

use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\Assert as PHPUnit;
use SebastianBergmann\CodeCoverage\Report\PHP;
use StatonLab\TripalTestSuite\Exceptions\InvalidJSONException;

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
     * Convert response to string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getResponseBody();
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
     * @throws \StatonLab\TripalTestSuite\Exceptions\InvalidJSONException
     */
    public function json()
    {
        $json = json_decode($this->getResponseBody(), true);
        if (is_null($json)) {
            PHPUnit::fail('Unable to decode json response!');

            throw new InvalidJSONException('Unable to decode json response!');
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
            try {
                $data = $this->json();
            } catch (InvalidJSONException $exception) {
                PHPUnit::fail('Unable to decode json response!');
            }
        }

        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                PHPUnit::assertArrayHasKey($key, $data);

                // Prevent infinite loops
                if (is_null($data[$key])) {
                    continue;
                }

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
     * @return string
     */
    public function getResponseBody()
    {
        return (string)$this->response->getBody();
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
    public function assertSee($content)
    {
        $response = (string)$this->getResponseBody();
        PHPUnit::assertContains($content, $response, "Unable to find [$content] in response.");

        return $this;
    }
}
