Database Seeders
****************

Database seeders are also supported in TripalTestSuite. They give you the ability
to create reusable seeders that can be run using the ``tripaltest`` command line tool.

Example Seeders
================

Several example database seeders are provided in ``tests/DatabaseSeeders/examples``.  These will not run unless they are copied to ``tests/DatabaseSeeders/``.

Creating Database Seeders
=========================

DB seeders can also be created automatically using ``tripaltest``:

.. code-block:: bash

	./vendor/bin/tripaltest make:seeder ExampleTableSeeder


The above command will create ``ExampleTableSeeder.php`` in ``tests/DatabaseSeeders/`` pre-populated
with the necessary namespace, methods and properties.

Using Database Seeders
======================

DB seeders support an important method: ``up()``. The ``up()``
method is used to insert data into the database when the test class is initialized, making it available to all tests within the class. The following is an example of a Seeder class.

.. code-block:: php

	<?php

	namespace Tests\DatabaseSeeders;

	use StatonLab\TripalTestSuite\Database\Seeder;

	class UsersTableSeeder extends Seeder
	{
		/**
		 * Seeds the database with users.
		 */
		public function up()
		{
			$new_user = [
				'name' => 'test user',
				'pass' => 'secret',
				'mail' => 'test@example.com',
				'status' => 1,
				'init' => 'Email',
				'roles' => [
					DRUPAL_AUTHENTICATED_RID => 'authenticated user',
				],
			];

			// The first parameter is sent blank so a new user is created.
			user_save(new \stdClass(), $new_user);
		}
	}


Running Seeders
===============

You can also run the seeder manually by using the static ``seed()`` method. For example, within a test class,
you can run ``$seeder = UsersTableSeeder::seed()`` which runs the ``up()`` method and returns an initialized seeder
object. If you are using the ``DBTransaction`` trait, the data will be automatically rolled at the end of each test
function.

.. attention::
	Don't forget to import your seeder at the top of the test file:
	``use  Tests\DatabaseSeeders\UsersTableSeeder;``


The other option is to run it using ``tripaltest`` as follows

.. code-block:: bash

	# run all available seeders
	tripaltest db:seed

	# Run a specific seeder by providing the class name
	tripaltest db:seed ExampleSeeder


.. attention::

	Running the seeder manually in a test function with ``DBTransaction`` enabled,
	means that the data is available only to that function and nothing else. However,
	running it using ``tripaltest`` makes it always available unless explicitly deleted.


Retrieving Seeder Data
======================

If your seeder returns any data, you can obtain the returned record by manually running
the seeder in your test. See below for an example:

.. code-block:: php

	<?php
	// Seeder Class
	class MySeeder extends Seeder {
		public function up() {
			// Generate some data.
			$data = db_query(...);

			return $data;
		}
	}

	// Test Class
	class MyTest extends TripalTestCase {
		public function testExample() {
			$seeder = new MySeeder();
			$data = $seeder->up();

			// Run some tests using the generated data
			// ...
		}
	}


Using DevSeed for Quick Biological Data Seeding
===============================================

Tripal Test Suite ships with a default seeder called ``DevSeedSeeder``.  It is located at ``tests/DatabaseSeeders/``. This seeder provides a quick
and automated way of seeding your database with biological data such as organisms, mRNAs, BLAST
annotations and InterProScan annotations. The data in the default seeder is obtained
from `Tripal DevSeed <https://github.com/statonlab/tripal_dev_seed>`_, which is a developer
mini-set of biological data.


DevSeed uses factories and is therefore **only appropriate for testing and development** and should not be run on a production site.

.. attention::

	DevSeedSeeder.php becomes available after running ``tripaltest init``. The ``init`` command will
	not override existing files unless you specify the ``--force`` flag so it it's safe to run it to get only
	the DevSeeder.


By default, the DevSeed comes with all sub-loaders disabled.  To configure the DevSeed seeder, uncomment data types you need in the protected variables section. Then, you can run the seeder using ``tripaltest db:seed DevSeedSeeder``.

1. If you haven't already, copy the seeder from ``tests/DatabaseSeeders/examples`` to ``tests/DatabaseSeeders/``.
2. Open ``DatabaseSeeders/DevSeedSeeder.php``
3. You'll notice a few commented properties in the top of the file.
4. Uncomment and modify the properties to your need.
5. Carefully follow the instructions in this section.  All loaders require an organism as well, but some are dependent on previous loaders.
6. Next, run ``tripaltest db:seed DevSeedSeeder``
7. If the seeder runs successfully, you'll be able to see all the records in your Chado database.

The records provided by DevSeed are not published to your site as entities. You can do that
by adding ``$this->publish('CHADO_TABLE')`` at the end of the ``up()`` method of the ``DevSeedSeeder``.
Replace ``CHADO_TABLE`` with the name of the table such as ``feature`` for mRNAs and ``analysis`` for analyses.
Or, if you prefer, you can use the Tripal admin interface to publish the records.
