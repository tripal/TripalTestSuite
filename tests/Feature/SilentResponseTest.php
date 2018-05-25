<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException;
use StatonLab\TripalTestSuite\Services\SilentResponse;

class SilentResponseTest extends TestCase
{
    /**
     * @throws \StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException
     */
    public function testReturnsSilentResponse()
    {
        $output = silent(function () {
        });

        $this->assertInstanceOf(SilentResponse::class, $output);
    }

    /**
     * @throws \StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException
     */
    public function testReturnedValue()
    {
        $output = silent(function () {
            echo "My second test";

            return "Return value";
        });

        $this->assertEquals($output->getReturnValue(), 'Return value');
    }

    /**
     * @throws \StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException
     */
    public function testOutputIsCollected()
    {
        $output = silent(function () {
            echo "test";
        });

        $this->assertEquals($output->getContent(), 'test');
    }

    /**
     * @throws \StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException
     */
    public function testNonExistentStringCallables()
    {
        $this->expectException(FunctionNotFoundException::class);

        $output = silent('does not exist');

        $this->assertInstanceOf(SilentResponse::class, $output);
    }

    /**
     * @throws \StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException
     */
    public function testStringCallable()
    {
        $output = silent('str_begins_with', ['name', 'my name is']);

        $this->assertFalse($output->getReturnValue());
    }

    /**
     * @throws \StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException
     */
    public function testAssertReturnEqualsChecksTheCorrectValue()
    {
        // The following should pass
        $output = silent(function () {
            return true;
        });

        $output->assertReturnEquals(true);
    }
}
