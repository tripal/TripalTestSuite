Creating Tests
**************

Using ``tripaltest``, you can create test files pre-populated with all the requirements.
To create a new test, run the following command from your module's root directory:

.. code-block:: bash

	# Creates a test file called ExampleTest.php in the tests folder
	./vendor/bin/tripaltest make:test ExampleTest

	# Creates a test file called ExampleTest.php in tests/Features/Entities
	# This will automatically detect and configure the namespace of your script
	./vendor/bin/tripaltest make:test Features/Entities/ExampleTest



.. warning::

	You should not include ``tests/`` in your path, nor should you specify a file extension.


.. warning::

	Test names should end with ``Test`` for phpunit to recognize them.
