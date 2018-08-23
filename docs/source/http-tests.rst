Testing HTTP Requests
*********************

TripalTestSuite provides a comprehensive HTTP testing methods. It allows you to call
site urls and check that your Drupal menu items are working as expected.

For example, the following tests that the homepage is accessible and that the name of the
website is present in the response.

.. code-block:: php

	public function testHomePage() {
	  // Send a GET request
	  $response = $this->get('/')

	  // Verify the HTTP response code is "200 OK" and that the site name is visible
	  $response->assertStatus(200)
			   ->assertSee('My Site');
	}


Available HTTP Testing Methods
==============================

The following table describes all available HTTP methods in any test class that
extends TripalTestSuite:

.. csv-table::
	:header: "name", "parameters", "Description", "Return"

	``$this->get()``, **$url** ``string`` The url to call\n**$params** ``array`` Query parameters.\n**$headers** ``array`` Additional HTTP headers, Sends a GET request, ``TestResponse``
	``$this->post()``, **$url** ``string`` The url to call\n**$params** ``array`` Form request parameters.\n**$headers** ``array`` Additional HTTP headers, Sends a POST request, ``TestResponse``
	``$this->put()``, **$url** ``string`` The url to call\n**$params** ``array`` Query parameters.\n**$headers** ``array`` Additional HTTP headers, Sends a PUT request, ``TestResponse``
	``$this->patch()``, **$url** ``string`` The url to call\n**$params** ``array`` Query parameters.\n**$headers** ``array`` Additional HTTP headers, Sends a PATCH request, ``TestResponse``
	``$this->delete()``, **$url** ``string`` The url to call\n**$params** ``array`` Query parameters.\n**$headers** ``array`` Additional HTTP headers, Sends a DELETE request, ``TestResponse``


The ``TestResponse`` returned from the HTTP requests, provide the following set of assertion methods:

.. csv-table::
	:header: name, Parameters, Description

	``$response->assertStatus()``, **$code** ``int``, Verify the returned HTTP status code is equal to ``$code``
	``$response->assertSee()``, **$content** ``string``, Verify the given string is present in the returned response body (i, e HTML,  JSON,  etc)
	``$response->assertJsonStructure()``, **$structure** ``array``, Verifies that the returned JSON matches the given structure (see below for example)
	``$response->assertSuccessful()``, none, Verify the returned HTTP status code is between 200 and 299,  which are HTTP's successful response codes

