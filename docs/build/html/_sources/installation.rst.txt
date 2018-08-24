Installation
************

Within your Drupal module path (e,g ``sites/all/modules/my_module``), run the following.

.. code-block:: bash

	composer require statonlab/tripal-test-suite --dev


Automatic Set Up
================

This module will automatically configure your tests directory, PHPUnit bootstrap files, and travis
continuous integration file as well as provide an example test and an example database seeder to
get you started.

From your module's directory, execute:

.. code-block:: bash

	# You may specify the module name or leave it blank.
	# When left blank, the name of the current directory will be used as the module name.
	./vendor/bin/tripaltest init [MODULE_NAME]


This will
- Set up the testing framework by creating the tests directory, phpunit.xml and tests/bootstrap.php
- Create an example test in tests/ExampleTest.php
- Create a DatabaseSeeders folder and an example seeder in tests/DatabaseSeeders/UsersTableSeeder.php
- Create DevSeedSeeder.php in DatabaseSeers. See the [DevSeed section] to learn more about automatically populating the database with biological data.
- Create an example ``.env`` file.
- Create ``.travis.yml`` configured to use a tripal3 docker container to run your tests

You can now write tests in your ``tests`` folder.  To enable continuous
integration testing, push your module to github and `enable Travis CI <https://travis-ci.org/>`_.

Forcing initialization
======================

To force replacing files that tripaltest have perviously generated, you can use the
``--force`` flag. You will need to confirm this flag by typing ``y`` and hitting ``enter``.

.. code-block:: bash

	./vendor/bin/tripaltest init --force

