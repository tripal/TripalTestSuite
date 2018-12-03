Installation
************


Adding Test Suite to a New Project
===================================

Within your Drupal module path (e.g. ``sites/all/modules/my_module``), run the following.

.. code-block:: bash

	composer require statonlab/tripal-test-suite --dev


Automatic Set Up
================

TestSuite can automatically configure your tests directory, PHPUnit bootstrap files, and travis
continuous integration file as well as provide an example test and an example database seeder to
get you started.

From your module's directory, execute:

.. code-block:: bash

	# You may specify the module name or leave it blank.
	# When left blank, the name of the current directory will be used as the module name.
	./vendor/bin/tripaltest init [MODULE_NAME]


This will:

- Set up the testing framework by creating the tests directory, ``phpunit.xml`` and ``tests/bootstrap.php``
- Create an example test in ``tests/ExampleTest.php``
- Create a DatabaseSeeders folder and an example seeder in ``tests/DatabaseSeeders/UsersTableSeeder.php``
- Create ``DevSeedSeeder.php`` in DatabaseSeers. See the :ref:`Database Seeders` section to learn more about automatically populating the database with biological data.
- Create an example ``.env`` file.
- Create ``.travis.yml`` configured to use a tripal3 docker container to run your tests

You can now write tests in your ``tests`` folder.  To enable continuous
integration testing, push your module to github and `enable Travis CI <https://travis-ci.org/>`_.

Environment Variables
=====================

When the ``init`` command is run (see: :ref:`Automatic Set Up`), an ``example.env`` file is created in ``tests/``.
This file should be copied to ``tests/.env`` and modified to describe **your specific Drupal environment**.

.. code-block:: bash

	# tests/.env
	BASE_URL=http://localhost
	DRUPAL_ROOT=/var/www/html
	FAKER_LOCALE=en_US

* BASE_URL: The URL of your site.  This is where you point your browser to view your site.
* DRUPAL_ROOT: The absolute path to your Drupal site.  On Unix machines, this is often ``/var/www/html``, but it may be different on your setup.
* FAKER_LOCALE: Test Suite uses the `Faker package <https://github.com/fzaninotto/Faker>`_ to create fake data in factories.  You can change the locale setting for the fake words used.


If the ``Drupal_ROOT`` variable is not set correctly, Drupal may not be bootstrapped correctly, and your tests may fail.


Forcing initialization
======================

To force replacing files that tripaltest have perviously generated, you can use the
``--force`` flag. You will need to confirm this flag by typing ``y`` and hitting ``enter``.

.. code-block:: bash

	./vendor/bin/tripaltest init --force

Joining an Existing Project
===========================

If Test Suite is already configured for a project (for example, if you clone Tripal and want to run tests), the ``composer.json`` file already specifies what version of Test Suite is in use.  To install it, execute the below command.

.. code-block:: bash

	composer install

.. warning::

	Before you can run tests, you'll first need to configure your :ref:`Environment Variables` in the ``.env`` file.

