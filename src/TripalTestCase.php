<?php

namespace StatonLab\TripalTestSuite;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\Concerns\InteractsWithAuthSystem;
use StatonLab\TripalTestSuite\Concerns\PublishesData;

abstract class TripalTestCase extends TestCase
{
    use PublishesData,
        InteractsWithAuthSystem;

    /**
     * @var array
     */
    protected $_includedTraits = [];

    /**
     * Set up the environment.
     *
     * @throws \StatonLab\TripalTestSuite\Exceptions\TripalTestSuiteException
     */
    protected function setUp()
    {
        $this->_includedTraits = array_reverse(class_uses_recursive(static::class));
        if (isset($this->_includedTraits[DBTransaction::class])) {
            $this->DBTransactionSetUp();
        }
    }

    /**
     * Tear down the environment.
     */
    protected function tearDown()
    {
        if (isset($this->_includedTraits[DBTransaction::class])) {
            $this->DBTransactionTearDown();
        }

        if(isset($this->_includedTraits[InteractsWithAuthSystem::class])) {
            $this->authSystemTearDown();
        }
    }
}
