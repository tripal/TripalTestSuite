<?php

namespace Test\Feature;

use StatonLab\TripalTestSuite\Mocks\TestResponseMock;
use StatonLab\TripalTestSuite\TripalTestCase;

class TestResponseTest extends TripalTestCase
{
    protected $response;

    /**
     * Creates a TestResponse instance pre-populated with a mock response.
     */
    public function setUp()
    {
        parent::setUp();

        $this->response = new TestResponseMock([
            'body' => [
                'string' => 'value',
                'key' => [
                    'values' => [
                        'string' => 'nested',
                    ],
                ],
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    /** @test */
    public function testJsonMethodReturnsAnArray()
    {
        $json = $this->response->json();

        $this->assertTrue(is_array($json), 'Unable to verify that TestResponse::json returns an array');
    }

    /** @test */
    public function testResponseJsonAssertions()
    {
        $this->response->assertJsonStructure([
            'string',
            'key' => [
                'values' => [
                    'string',
                ],
            ],
        ]);
    }

    /** @test */
    public function testResponseHasCorrectStatusCode()
    {
        $this->assertEquals($this->response->getStatusCode(), 200);
        $this->response->assertStatus(200);
    }

    /** @test */
    public function testVisibilityAssrtion() {
        $this->response->assertSee('values');
    }
}
