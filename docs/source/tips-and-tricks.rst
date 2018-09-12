Tips and Tricks for Writing Good Tests
****************************************


Generating Fake Variables: Faker
============================================

.. note::

  Are you creating fake values to insert into Chado?  If so, check out **Factories** which will generate all the fake values automatically.  For example:


  .. code-block:: php

  	# Generates 100 controlled vocabularies.
  	# @return an array of vocabularies
  	$controlledVocabs = factory('chado.cv', 100)->create()
  


The recommended method to create fake values for use in testing is to use the PHP Faker library by **@fzaninotto**: https://github.com/fzaninotto/Faker. To use this library in your Tripal tests, simply include it at the top of the class:

.. code-block:: php

  use StatonLab\TripalTestSuite\DBTransaction;
  use StatonLab\TripalTestSuite\TripalTestCase;
  use Faker\Factory;

  class ExampleTest extends TripalTestCase {
    /**
     * Stuff
     */
  }


Then instantiate it in your test method and create fake data using one of the various methods available through the library.

.. code-block:: php

  private function create_version() {

      // Generate a fake version.
      $faker = Factory::create();
      $version = $faker->randomFloat(2, 1, 5);
      return $version;

  }


More more information on what is provided by the fake library, check out their documentation here: github.com/fzaninotto/Faker

Test Guidelines
======================
See also: `The Tripal test guidelines <https://github.com/tripal/tripal/blob/7.x-3.x/tests/README.md>`_.  Tests you write that are included in the core Tripal repository should follow these standards.
