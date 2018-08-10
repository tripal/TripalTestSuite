<?php

namespace StatonLab\TripalTestSuite\Services;

use StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException;
use PHPUnit\Framework\Assert as PHPUnit;

class SilentResponse extends BaseResponse
{
    /**
     * Callable's printed output.
     *
     * @var string
     */
    protected $content;

    /**
     * Callable return value.
     *
     * @var mixed
     */
    protected $return;

    /**
     * SilentResponse constructor.
     *
     * @param string|callable $callable
     * @param array $arguments Optional. Array of arguments if callable is a string.
     * @throws \StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException
     */
    public function __construct($callable, array $arguments = [])
    {
        if (is_string($callable)) {
            return $this->runString($callable, $arguments);
        }

        return $this->runCallable($callable);
    }

    /**
     * Runs a user function.
     *
     * @param string $callable Function name.
     * @param array $arguments Arguments to pass to the user function.
     * @return $this
     * @throws \StatonLab\TripalTestSuite\Exceptions\FunctionNotFoundException
     */
    protected function runString($callable, array $arguments)
    {
        if (! function_exists($callable)) {
            throw new FunctionNotFoundException("Function $callable not found");
        }

        $this->startBuffer();

        $this->return = call_user_func_array($callable, $arguments);

        $this->endBuffer();

        return $this;
    }

    /**
     * Runs a `callable` function such as anonymous functions or invokable classes.
     *
     * @param callable $callable
     * @return $this
     */
    protected function runCallable(callable $callable)
    {
        $this->startBuffer();

        $this->return = $callable();

        $this->endBuffer();

        return $this;
    }

    /**
     * Starts the output buffer collection.
     */
    protected function startBuffer()
    {
        $this->content = '';

        ob_start(function ($buffer) {
            $this->content = $buffer;
        });

        putenv('TRIPAL_SUPPRESS_ERRORS=TRUE');
    }

    /**
     * Alias to getContent.
     *
     * @return string
     */
    public function getResponseBody()
    {
        return $this->getContent();
    }

    /**
     * Ends and cleans the buffer.
     */
    protected function endBuffer()
    {
        putenv('TRIPAL_SUPPRESS_ERRORS');
        ob_end_clean();
    }

    /**
     * Returns the printed output as a string.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getReturnValue()
    {
        return $this->return;
    }

    /**
     * Assert the return value of the callable equals the given value.
     *
     * @param $value
     *
     * @return $this
     */
    public function assertReturnEquals($value)
    {
        PHPUnit::assertEquals($value, $this->getReturnValue());

        return $this;
    }

    /**
     * Clean up memory.
     */
    public function __destruct()
    {
        $this->content = null;
        $this->return = null;
    }
}
