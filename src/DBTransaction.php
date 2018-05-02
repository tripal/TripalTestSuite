<?php
namespace StatonLab\TripalTestSuite;

trait DBTransaction
{
    /**
     * @var
     */
    protected $_transaction;

    /**
     * Start a Drupal DB Transaction.
     */
    protected function DBTransactionSetUp()
    {
        $this->_transaction = db_transaction(uniqid());
    }

    /**
     * Rollback the DB transaction.
     */
    protected function DBTransactionTearDown()
    {
        if ($this->_transaction) {
            $this->_transaction->rollback();
        }
    }
}
