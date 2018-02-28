<?php
/**
 * Created by PhpStorm.
 * User: Almsaeed
 * Date: 2/28/18
 * Time: 10:48 AM
 */

namespace Statonlab\TripalTestSuite;

trait DBTransaction
{
    /**
     * @var
     */
    protected $_transaction;

    /**
     * Start a Drupal DB Transaction.
     */
    protected function DBTransactionSetUp() {
        $this->_transaction = db_transaction();
    }

    /**
     * Rollback the DB transaction.
     */
    protected function DBTransactionTearDown() {
        $this->_transaction->rollback();
    }
}
