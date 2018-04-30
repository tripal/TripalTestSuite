<?php

namespace Test\Feature;

use StatonLab\TripalTestSuite\Concerns\MakesHTTPRequests;
use StatonLab\TripalTestSuite\Services\TestResponse;
use StatonLab\TripalTestSuite\TripalTestCase;

class TripalTestCaseHTTPTest extends TripalTestCase
{
    /** @test */
    public function testThatMakesHTTPRequestsTraitExists()
    {
        $uses = class_uses_recursive(TripalTestCase::class);
        $this->assertTrue(isset($uses[MakesHTTPRequests::class]));
    }

    /** @test */
    public function testThatGetMethodExists()
    {
        $this->assertTrue(method_exists($this, 'get'));
    }

    /** @test */
    public function testThatGetMethodReturnsTestResponse()
    {
        $response = $this->get('/');
        $this->assertTrue($response instanceof TestResponse);
    }

    /** @test */
    public function testThatPostMethodReturnsTestResponse()
    {
        $response = $this->post('/');
        $this->assertTrue($response instanceof TestResponse);
    }

    /** @test */
    public function testThatPutMethodReturnsTestResponse()
    {
        $response = $this->put('/');
        $this->assertTrue($response instanceof TestResponse);
    }

    /** @test */
    public function testThatDeleteMethodReturnsTestResponse()
    {
        $response = $this->delete('/');
        $this->assertTrue($response instanceof TestResponse);
    }

    /** @test */
    public function testThatPatchMethodReturnsTestResponse()
    {
        $response = $this->patch('/');
        $this->assertTrue($response instanceof TestResponse);
    }
}
