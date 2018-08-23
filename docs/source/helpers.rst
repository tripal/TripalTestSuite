Helper Methods
**************

TripalTestSuite provides a set of helper methods to automate tedious aspects of testing.

Silently Testing Printed Output
===============================

Since tests should run "silently", i.e. without printing output to the screen, we'd have to create
an output buffer to collect printed strings into a variable. In PHP, this can be done as such:

.. code-block:: php

	// Supress tripal errors
	putenv("TRIPAL_SUPPRESS_ERRORS=TRUE");
	ob_start();

	// Run the call
	echo "testing";
	$output = ob_get_contents();

	// Clean the buffer and unset tripal errors suppression
	ob_end_clean();
	putenv("TRIPAL_SUPPRESS_ERRORS");


However, TripalTestSuite provides a ``silent()`` method that automates this process, provides helpful assertions
and supports larger strings. Example usage:

.. code-block:: php

	$output = silent(function() {
	  echo "testing";
	});
	$output->assertSee('testing'); // true!


**WARNING:** This method has a maximum string size to avoid memory leaks. The size is set in PHP's ini file
as ``output_buffering``, which by default is set to 4KB. If you would like to collect larger strings, you must
adjust your PHP settings.

Assertions and Methods
======================

The silent method returns a SilentResponse which provides the following methods.

|Method|Arguments|Description|
|------|---------|-----------|
|``assertSee()``|``$value`` mixed|Asserts that the given value is present in the suppressed printed output|
|``assertReturnEquals()``|``$value`` mixed| Asserts that the given value equals the returned value from the called function|
|``assertJsonStructure()``|``$strcture`` array<br>``$data`` array **Optional**|Asserts that the given stricture matches that of the suppressed printed output|
|``getContent()``|None|Get the suppressed printed content as a string|
|``getReturnValue()``|None|Get the returned value from the called function|

**Examples**

.. code-block:: php

	$output = silent(function() {
		drupal_json_output(['key' => 'value']);
		return true;
	});

	$output->assertSee('value')
		   ->assertJsonStructure(['key'])
		   ->assertReturnEquals(true);


You can also call methods directly in the Callable function:

.. code-block:: php

	// Assume we have the following function
	function tripal_print_message($message) {
	  echo $message;
	}

	$output = silent(function() {
	  tripal_print_message('tripal test suite');
	});
	$output->assertSee('test');

	// Get the output as a string
	$rawOtput = $output->getContent();


Access Private and Protected Properties and Methods of Objects
==============================================================

TripalTestSuite provides a ``reflect()`` method that accepts an object
and makes all of the properties and methods public and available
for testing. Assume we have the following class:

.. code-block:: php

	class PrivateClass
	{
		private $private;

		public function __construct($private = 'private')
		{
			$this->private = $private;
		}

		protected function myProtected()
		{
			return 'protected';
		}

		private function privateWithArgs($one, $two)
		{
			return $one.' '.$two;
		}
	}


Because of the functions and properties of the class are private or protected, we
normally would not be able to access any of them. However, we can force access
using the reflect helper. See below for an examples.

**Accessing Methods**

.. code-block:: php

	// Pass an initialized class to the reflect method
	$myObject = new PrivateClass();
	$privateClass = reflect($myObject);

	// Accessing protected methods
	$value = $privateClass->myProtected();
	$this->assertEquals('protected', $value);

	// Accessing private methods with arguments
	$value = $privateClass->privateWithArgs('one', 'two');
	$this->assertEquals('one two', $value);


**Accessing Properties**

.. code-block:: php

	// Pass an initialized class to the reflect method
	$myObject = new PrivateClass();
	$privateClass = reflect($myObject);

	$this->assertEquals('private', $privateClass->private);

