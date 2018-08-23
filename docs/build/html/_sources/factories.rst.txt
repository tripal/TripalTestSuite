Factories
*********

DB factories provide a method to populate the database with fake data. Using factories, you
won't have to run SQL queries to populate the Database in every test. Since they are reusable,
you can define one factory for each table and use them across all tests.
Usage example:

.. code-block:: php

	# Generates 100 controlled vocabularies.
	# @return an array of vocabularies
	$controlledVocabs = factory('chado.cv', 100)->create()


Factories should **only be used for testing and development purposes**.

Defining Factories
==================

Factories live in ``tests/DataFactory.php``. If you don't have that file, create it. Note that this file
is auto created with ``tripaltest init``.

Example DataFactory file:

.. code-block:: php

	<?php

	use StatonLab\TripalTestSuite\Database\Factory;

	Factory::define('chado.cv', function (Faker\Generator $faker) {
		return [
			'name' => $faker->name,
			'definition' => $faker->text,
		];
	});


As shown in the example above, using ``Factory::define()``, we can define new factories.
The define method takes the following parameters:

.. csv-table::
	:header: "Parameter", "Type", "Description", "Example"

	"$table", "``string``", "The table name preceded with the schema name if the schema is not public", "``chado.cv`` or ``node``"
	"$callback", "``callable``", "The function that generates the array. A ``Faker\Generator`` instance is automatically passed to the callable", "see above for example"
	"$primary_key", "``string``", "**OPTIONAL** The primary key for the given table. Primary keys auto discovered for CHADO tables only. If the factory wasn't able to find the primary key, an ``Exception`` will be thrown", "``nid`` or ``cv_id``"


Using Factories
===============

Once defined, factories can be used in test files directly or in database seeders.
Usage:

.. code-block:: php

	# Create a single CV record
	$cv = factory('chado.cv')->create();
	echo "$cv->name\n";

	# Create 100 CV records
	$cvs = factory('chado.cv', 100)->create();

	foreach ($cvs as $cv) {
	  echo "$cv->name\n";
	}


Overriding Defaults
===================

Sometimes you need to override a column to be a static predictable value. The ``create()`` method accepts an array of values
to override the faker data with. Example:

.. code-block:: php

	# Let's make sure the cvterm has a specific cv id
	$cv = factory('chado.cv')->create();
	$cv_term = factory('chado.cvterm', 100)->create([
		'cv_id' => $cv->cv_id,
	])


The above example creates 100 cv terms that have the same cv_id.

Factories should **only be used for testing and development purposes**.  This is because they are **recursive** and create the records linked via foreign key.  They do this **even if you override the default** for the linked record.
