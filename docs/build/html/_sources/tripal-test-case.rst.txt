TripalTestCase
**************

Test classes should extend the TripalTestCase class. Once extended, bootstrapping
Drupal and reading your ``.env`` file is done automatically when the first test is run.

.. code-block:: php

	namespace Tests;

	use StatonLab\TripalTestSuite\TripalTestCase;

	class MyTest extends TripalTestCase {
	}


.. attention::

	If you define a ``setUp`` method within a test class, be sure to call ``parent::setUp``!
