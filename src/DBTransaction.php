<?php

namespace StatonLab\TripalTestSuite;

trait DBTransaction
{
    /**
     * @var \DatabaseTransaction
     */
    protected $_transaction;

    /**
     * Start a Drupal DB Transaction.
     */
    protected function DBTransactionSetUp()
    {
        $this->_transaction = db_transaction(uniqid());

        $shutdown = '\\StatonLab\\TripalTestSuite\\DBTransaction::rollbackTransaction';
        register_shutdown_function($shutdown, $this->_transaction);
    }

    /**
     * Rolls back a transaction in case of shutdown.
     *
     * @param \DatabaseTransaction $transaction
     */
    public static function rollbackTransaction($transaction)
    {
        if ($transaction instanceof \DatabaseTransaction) {
            try {
                $transaction->rollback();
            } catch (\DatabaseTransactionNoActiveException $exception) {

            }
        }
    }

    /**
     * Rollback the DB transaction.
     */
    protected function DBTransactionTearDown()
    {
        if ($this->_transaction) {
            $this->_transaction->rollback();

            // Null the transaction so the shutdown function doesn't attempt to rollback again
            $this->_transaction = null;
        }
    }
}
