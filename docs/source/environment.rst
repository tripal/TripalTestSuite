Environment Variables
*********************

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
