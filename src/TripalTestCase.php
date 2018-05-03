<?php

namespace StatonLab\TripalTestSuite;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\Concerns\InteractsWithAuthSystem;
use StatonLab\TripalTestSuite\Concerns\MakesHTTPRequests;
use StatonLab\TripalTestSuite\Concerns\PublishesData;

abstract class TripalTestCase extends TestCase
{
    use MakesHTTPRequests,
        //InteractsWithAuthSystem,
        PublishesData;

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

        //$this->authSystemTearDown();
        $this->_clearRequestData();
    }

    /**
     * Destroy session and clear request body.
     */
    protected function _clearRequestData()
    {
        $_POST = [];
        $_GET = [];
    }
}
