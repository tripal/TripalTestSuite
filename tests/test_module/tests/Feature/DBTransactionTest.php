<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\DBTransaction;
use StatonLab\TripalTestSuite\TripalTestCase;

class DBTransactionTest extends TestCase
{
    use DBTransaction;

    /**
     * Bootstrap Drupal.
     *
     * @throws \ReflectionException
     */
    protected function setUp()
    {
        $test_case = new TripalTestCase();
        $method = $this->getMethod('_bootstrapDrupal');
        $method->invoke($test_case);
    }

    /** @test */
    public function should_start_a_transaction()
    {
        $this->DBTransactionSetUp();
        $this->assertInstanceOf(\DatabaseTransaction::class, $this->_transaction);
        $this->DBTransactionTearDown();
    }

    /** @test */
    public function should_fail_to_find_record_after_transaction_has_ended()
    {
        $this->DBTransactionSetUp();

        $count = db_query('SELECT COUNT(*) FROM {test_module}')->fetchField();

        // Insert something into the db
        db_insert('test_module')->fields([
            'name' => 'test',
            'value' => 'test',
        ])->execute();

        // Make sure it exists in the db
        $newCount = db_query('SELECT COUNT(*) FROM {test_module}')->fetchField();

        $this->assertEquals($count + 1, $newCount);

        // End the transaction
        $this->DBTransactionTearDown();

        // Now the record should not exit
        $finalCount = db_query('SELECT COUNT(*) FROM {test_module}')->fetchField();

        $this->assertEquals($newCount - 1, $finalCount);
    }

    /**
     * Get a private or protected methods from a given class.
     *
     * @param string $method_name Method name
     * @param string $class_name Class name. Defaults to TripalTestCase
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    protected function getMethod($method_name, $class_name = '')
    {
        if (empty($class_name)) {
            $class_name = TripalTestCase::class;
        }

        $reflection = new \ReflectionClass($class_name);
        $method = $reflection->getMethod($method_name);
        $method->setAccessible(true);

        return $method;
    }
}
