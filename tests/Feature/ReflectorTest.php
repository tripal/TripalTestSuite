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
        $private = reflect(new PrivateClass());
        $this->assertEquals('private', $private->myPrivate());
    }

    /**
     * @throws \Exception
     */
    public function testMakingProtectedMethodsAccessible()
    {
        $private = reflect(new PrivateClass());
        $this->assertEquals('protected', $private->myProtected());
    }

    /**
     * @throws \Exception
     */
    public function testMakingPublicMethodsAccessible()
    {
        $private = reflect(new PrivateClass());
        $this->assertEquals('public', $private->myPublic());
    }

    /**
     * @throws \Exception
     */
    public function testMakingPrivateMethodsWithArgsAccessible()
    {
        $private = reflect(new PrivateClass());
        $this->assertEquals('arg1 arg2', $private->privateWithArgs('arg1', 'arg2'));
    }

    /**
     * @throws \Exception
     */
    public function testAccessingPrivateProperties() {
        $private = reflect(new PrivateClass());
        $this->assertEquals('private', $private->private);
    }

    /**
     * @throws \Exception
     */
    public function testAccessingProtectedProperties() {
        $private = reflect(new PrivateClass());
        $this->assertEquals('protected', $private->protected);
    }

    /**
     * @throws \Exception
     */
    public function testAccessingPublicProperties() {
        $private = reflect(new PrivateClass());
        $this->assertEquals('public', $private->public);
    }

    /**
     * @throws \Exception
     */
    public function testManipulatingPrivateProperties() {
        $private = reflect(new PrivateClass());
        $this->assertEquals('private', $private->getPrivate());
        $private->private = 'not private';
        $this->assertEquals('not private', $private->getPrivate());
        $this->assertEquals('not private', $private->private);
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

    private function getPrivate() {
        return $this->private;
    }
}
