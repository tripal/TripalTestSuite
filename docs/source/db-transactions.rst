Using DB Transactions to Automatically Rollback Database Changes
****************************************************************

Using DB transactions cleans up the database after every test by rolling back
the database to the original state before the test started. Therefore, anything
added to the database in one test function will not be available for the next
function. If you'd like data to be available for all of the tests, see `database
seeders <https://github.com/statonlab/TripalTestSuite#database-seeders>`_ above.

To activate DB Transactions, simply add the DBTransaction trait to your test class:

.. code-block:: php

	namespace Tests;

	use StatonLab\TripalTestSuite\TripalTestCase;
	use StatonLab\TripalTestSuite\DBTransaction;

	class MyTest extends TripalTestCase {
		use DBTransaction;
	}


The trait will automatically activate DB transactions and rollback the database when the test is finished.

.. warning::

	Using ``@dataProvider`` does not honor database transactions.
	If your dataProvider creates database records, call it directly
	in the test function instead of using ``@dataProvider`` in you doc block.

.. warning::

	If the code you are testing requires a transaction, Postgres
	will fail since it does not support nested transactions.
