Running Tests
*************

Tripal Test Suite auto installs PHPunit as part of it's dependencies in composer.json.
Therefore, running tests in Tripal Test Suite is done via phpunit as such:

.. code-block:: bash

    ./vendor/bin/phpunit


The command above, will read your ``phpunit.xml`` and runs the tests accordingly.
