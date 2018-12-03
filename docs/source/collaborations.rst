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

- Fork this repository ([see guide on GitHub](https://help.github.com/articles/fork-a-repo/))
- Clone your fork `git clone https://github.com/USERNAME/TripalTestSuite.git`  (where USERNAME is your Github username).
- Navigate to TripalTestSuite `cd TripalTestSuite`
- Install dependencies `composer install`
- Boot up Docker `docker-compose up -d`
- Finally, checkout a new branch and make your changes `git checkout -b my-branch-name` (please make the branch name descriptive)

.. attention::

	If introducing new features, please provide unit tests for the new features.

Performing Tests
----------------

We require that all new features and contributions are fully unit-tested. There are
two types of tests:

