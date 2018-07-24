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
        $this->reflection = new \ReflectionClass($class);

        $this->object = $class;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->object, $arguments);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($this->object);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $property = $this->reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->setValue($value);
    }
}
