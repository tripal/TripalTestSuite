<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use StatonLab\TripalTestSuite\DBTransaction;
use StatonLab\TripalTestSuite\Services\BootstrapDrupal;

class DBTransactionTest extends TestCase
{
    use DBTransaction;

    /**
     * Bootstrap Drupal.
     *
     * @throws \ReflectionException|\Exception
     */
    protected function setUp(): void
    {
        (new BootstrapDrupal())->run();
    }

    /** @test */
    public function shouldStartATransaction()
    {
        $this->DBTransactionSetUp();
        $this->assertInstanceOf(\DatabaseTransaction::class, $this->_transaction);
        $this->DBTransactionTearDown();
    }

    /** @test */
    public function shouldFailToFindRecordAfterTransactionHasEnded()
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

    /** @test */
    public function shouldFailToFindFactoryCreatedRecordAfterTransactionEnds()
    {
        $this->DBTransactionSetUp();

        $cvs = factory('chado.cv', 100)->create();

        $ids = array_map(function ($cv) {
            return $cv->cv_id;
        }, $cvs);

        $count = (int)db_query('SELECT COUNT(*) FROM chado.cv WHERE cv_id IN (:cv_id)', [
            ':cv_id' => $ids,
        ])->fetchField();

        $this->assertEquals($count, 100);

        $this->DBTransactionTearDown();

        $count = (int)db_query('SELECT COUNT(*) FROM chado.cv WHERE cv_id IN (:cv_id)', [
            ':cv_id' => $ids,
        ])->fetchField();

        $this->assertEquals($count, 0);
    }
}
