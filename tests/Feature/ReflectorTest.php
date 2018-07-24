<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class ReflectorTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testMakingPrivateMethodsAccessible()
    {
        $privateClass = reflect(new PrivateClass());
        $this->assertEquals('private', $privateClass->myPrivate());
    }

    /**
     * @throws \Exception
     */
    public function testMakingProtectedMethodsAccessible()
    {
        $privateClass = reflect(new PrivateClass());
        $this->assertEquals('protected', $privateClass->myProtected());
    }

    /**
     * @throws \Exception
     */
    public function testMakingPublicMethodsAccessible()
    {
        $privateClass = reflect(new PrivateClass());
        $this->assertEquals('public', $privateClass->myPublic());
    }

    /**
     * @throws \Exception
     */
    public function testMakingPrivateMethodsWithArgsAccessible()
    {
        $privateClass = reflect(new PrivateClass());
        $this->assertEquals('arg1 arg2', $privateClass->privateWithArgs('arg1', 'arg2'));
    }

    /**
     * @throws \Exception
     */
    public function testAccessingPrivateProperties() {
        $privateClass = reflect(new PrivateClass());
        $this->assertEquals('private', $privateClass->private);
    }

    /**
     * @throws \Exception
     */
    public function testAccessingProtectedProperties() {
        $privateClass = reflect(new PrivateClass());
        $this->assertEquals('protected', $privateClass->protected);
    }

    /**
     * @throws \Exception
     */
    public function testAccessingPublicProperties() {
        $privateClass = reflect(new PrivateClass());
        $this->assertEquals('public', $privateClass->public);
    }
}

class PrivateClass
{
    protected $protected;

    public $public;

    private $private;

    public function __construct($protected = 'protected', $private = 'private', $public = 'public')
    {
        $this->protected = $protected;
        $this->private = $private;
        $this->public = $public;
    }

    private function myPrivate()
    {
        return 'private';
    }

    protected function myProtected()
    {
        return 'protected';
    }

    protected function myPublic()
    {
        return 'public';
    }

    private function privateWithArgs($one, $two)
    {
        return $one.' '.$two;
    }
}
