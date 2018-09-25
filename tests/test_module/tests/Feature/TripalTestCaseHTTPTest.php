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
    public function testThatGetMethodReturnsTestResponse()
    {
        $response = $this->get('/');
        $this->assertTrue($response instanceof TestResponse);
        $response->assertSuccessful()
        ->assertSee(variable_get('site_name'));
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

    /** @test */
    public function testNotFoundRoute()
    {
        $response = $this->get('/never-in-a-million-years-will-this-page-exist-'.uniqid());

        $response->assertStatus(404);
    }

    /** @test */
    public function testUnauthorizedAccess()
    {
        $response = $this->get('/admin');
        $response->assertStatus(403);
    }

    /** @test */
    public function testAdminAccess()
    {
        $this->actingAs(1);
        $response = $this->get('/admin');
        $response->assertStatus(200);
    }

    /** @test */
    public function testPublicRoute()
    {
        $response = $this->get('testing/test_module');

        $response->assertSuccessful()->assertSee('testing html');
    }

    /** @test */
    public function testRequestForm()
    {
        $response = $this->get('testing/test_module_form');

        $response->assertSuccessful()->assertSee('Please enter your first name.');
    }
}
