<?php

namespace StatonLab\TripalTestSuite\Services;

use PHPUnit\Framework\Assert as PHPUnit;
use StatonLab\TripalTestSuite\Exceptions\InvalidJSONException;

abstract class BaseResponse
{
    /**
     * Return response content.
     *
     * @return string
     */
    abstract public function getResponseBody();

    /**
     * Assert string exists in response body.
     *
     * @param string $content
     * @return $this
     */
    public function assertSee($content)
    {
        $response = (string)$this->getResponseBody();
        PHPUnit::assertContains($content, $response, "Unable to find [$content] in response.");

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
}
