<?php

namespace StatonLab\TripalTestSuite\Services;

class Reflector
{
    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @var \stdClass
     */
    protected $object;

    /**
     * Reflector constructor.
     *
     * @param $class
     * @throws \ReflectionException
     */
    public function __construct($class)
    {
        $this->object = $class;

        $this->reflection = new \ReflectionClass($this->object);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->object, $arguments);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($this->object);
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($this->object, $value);
    }
}
