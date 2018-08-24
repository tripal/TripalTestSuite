User Authentication
*******************

Authenticating a user with TripalTestSuite is very simple using the ``actingAs`` method. When
authenticating a user with TripalTestSuite, the user is automatically signed out by the end
of each test method, which guarantees that your other tests are using the anonymous user
unless you specifically tell it otherwise.

.. code-block:: php

	public function testExample() {
	  // Authenticate the superuser who has an id 1
	  $this->actingAs(1);

	  // Verify that the user is the admin user
	  global $user;
	  $this->assertTrue(1 === $user->uid);
	}


.. attention::

	The ``actingAs`` method can take a user id to authenticate or a Drupal user object.
