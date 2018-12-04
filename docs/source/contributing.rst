Contribution Guidelines
***********************

Thank you for your interest in contributing to Tripal Test Suite! This
guide should help you get setup quickly to start contributing.

Creating a Development Environment
----------------------------------

It is highly recommended to use Docker to create your development environment.
This would guarantee that we share identical environments and help reduce
debugging efforts.

To get started:

- Fork this repository (`see guide on GitHub <https://help.github.com/articles/fork-a-repo/>`_)
- Clone your fork ``git clone https://github.com/USERNAME/TripalTestSuite.git``  (where USERNAME is your Github username).
- Navigate to TripalTestSuite ``cd TripalTestSuite``
- Install dependencies ``composer install``
- Boot up Docker ``docker-compose up -d``
- Finally, checkout a new branch and make your changes ``git checkout -b my-branch-name`` (please make the branch name descriptive)

.. attention::

	If introducing new features, please provide unit tests for the new features.

Performing Tests
----------------

We require that all new features and contributions are fully unit-tested. There are
two types of tests:

- TripalTestSuite features that do not require Drupal (such as the ``reflect`` and ``silent`` helper methods)
- Features that depend on Drupal (such as factories)

If your feature doesn't require Drupal, the tests should go to ``tests/Feature``. If it does
require to be part of a module, you can use the test_module in ``tests/test_module``. Within the
module, there will be a ``tests`` folder where you can test your contributions.

Structure
---------

All code for this package should go in the ``src/`` directory. In there, you'll find:

- ``Concerns``: Add-on features that must be `PHP traits <http://php.net/manual/en/language.oop5.traits.php>`_. Traits are ideal when a feature makes use of the main class functionality or can be be chained. For example, the `InteractsWithAuthSystem` offers `actingAs()` method that can be chained with HTTP requests as such: `$this->actingAs(1)->get('/myform');`.
- ``Console``: Any commands that ``tripaltest`` offers go here. Note that all commands must be registered in ``kernel.php``.
- ``Database``: Any database related features such as support for factories, publishing entities and seeding.
- ``Exceptions``: Any custom Exceptions.
- ``Helpers``: Functions that perform simple operations or make classes easier to access. For example, ``factory()`` is a helper method.
- ``Mocks``: Objects that simulate the behavior of other (real) objects. `Read more about mocks <https://medium.com/@piraveenaparalogarajah/what-is-mocking-in-testing-d4b0f2dbe20a>`_
- ``Services``: Isolated classes that offer special functionality. Most classes that should be used within a test, such as ``SilentResponse``, should also offer a helper method. For example, the ``Factory`` class offers the ``factory()`` helper.

Including PHP Files
-------------------

Tripal Test Suite uses ``PSR-4`` loading standards and therefore there is no need for you to manually include
any file.

.. warning::

	Namespaces matter in class/trait loading. The namespace must contain all sub directories.
	For example, the ``Concerns/PublishesData.php`` file must have the namespace ``namespace StatonLab\TripalTestSuite\Concerns;``.
	All namespaces should be preceded with ``StatonLab\TripalTestSuite``.


Code Style
----------

We conform to `PSR-2 <https://www.php-fig.org/psr/psr-2/>`_ code style

