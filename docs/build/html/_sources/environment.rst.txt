Environment Variables
*********************

You can specify the Drupal web root path in ``tests/.env``.

.. code-block:: bash

	# tests/.env
	BASE_URL=http://localhost
	DRUPAL_ROOT=/var/www/html
	FAKER_LOCALE=en_US


This allows TripalTestSuite to bootstrap the entire Drupal framework and make it available in your tests.
