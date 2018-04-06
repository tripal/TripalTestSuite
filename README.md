[![Build Status](https://travis-ci.org/statonlab/TripalTestSuite.svg?branch=master)](https://travis-ci.org/statonlab/TripalTestSuite) [![DOI](https://zenodo.org/badge/123318173.svg)](https://zenodo.org/badge/latestdoi/123318173)

**TripalTestSuite** is a composer package that handles
common test practices such as bootstrapping Drupal
before running the tests, creating test file, and creating
and managing database seeders (files that seed the database
with data for use in testing).

### Installation
Within your Drupal module path (e,g `sites/all/modules/my_module`), run the following.
```bash
composer require statonlab/tripal-test-suite --dev
```

#### Automatic Set Up
This module will automatically configure your tests directory, PHPUnit bootstrap files, and travis 
continuous integration file as well as provide an example test and an example database seeder to
get you started. 

From your module's directory, execute:
```bash
# You may specify the module name or leave it blank.
# When left blank, the name of the current directory will be used as the module name.
./vendor/bin/tripaltest init MODULE_NAME 
```

This will 
- Set up the testing framework by creating the tests directory, phpunit.xml and tests/bootstrap.php
- Create an example test in tests/ExampleTest.php
- Create a DatabaseSeeders folder and an example seeder in tests/DatabaseSeeders/UsersTableSeeder.php
- Create an example `.env` file.
- Create `.travis.yml` configured to use a tripal3 docker container to run your tests  

You can now write tests in your `tests` folder.  To enable continuous
integration testing, push your module to github and [enable Travis CI](https://travis-ci.org/).

### Usage

#### Running Tests
Tripal Test Suite auto installs PHPunit as part of it's dependencies in composer.json.
Therefore, running tests in Tripal Test Suite is done via phpunit as such:
```bash
./vendor/bin/phpunit
```
The command above, will read your `phpunit.xml` and runs the tests accordingly.

#### Creating Tests
Using `tripaltest`, you can create test files pre-populated with all the requirements.
To create a new test, run the following command from your module's root directory:
```bash
# Creates a test file called ExampleTest.php in the tests folder
./vendor/bin/tripaltest make:test ExampleTest

# Creates a test file called ExampleTest.php in tests/Features/Entities
# This will automatically detect and configure the namespace of your script
./vendor/bin/tripaltest make:test Features/Entities/ExampleTest
```
Note: Test names should end with `Test` for phpunit to recognize them.

#### Database Seeders
Database seeders are also supported in TripalTestSuite. They give you the ability
to create reusable seeders that can run automatically before entering the testing
stage and get rolled back automatically after the tests are completed.

##### Creating Database Seeders
DB seeders can also be created automatically using `tripaltest`:
```bash
./vendor/bin/tripaltest make:seeder ExampleTableSeeder
```
The above command will create `ExampleTableSeeder.php` in `tests/DatabaseSeeders/` pre-populated
with the necessary namespace, methods and properties.

#### Using Database Seeders
DB seeders support two important methods, `up()` and `down()`. The `up()`
method is used to insert data into the database while the `down()` method
is used to clean up the inserted data. The following is an example of a Seeder class.

```php
<?php

namespace Tests\DatabaseSeeders;

use StatonLab\TripalTestSuite\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Whether to run the seeder automatically before
     * starting our tests and destruct them automatically
     * once the tests are completed.
     *
     * If you set this to false, you can run the seeder
     * from your tests directly using UsersTableSeeder::seed()
     * which returns an instance of the class the you can use
     * to run the down() method whenever required.
     *
     * @var bool
     */
    public $auto_run = true;

    /**
     * The users that got created.
     * We save this here to have them easily deleted
     * in the down() method.
     *
     * @var array
     */
    protected $users = [];

    /**
     * Seeds the database with users.
     */
    public function up()
    {
        $new_user = [
            'name' => 'test user',
            'pass' => 'secret',
            'mail' => 'test@example.com',
            'status' => 1,
            'init' => 'Email',
            'roles' => [
                DRUPAL_AUTHENTICATED_RID => 'authenticated user',
            ],
        ];

        // The first parameter is sent blank so a new user is created.
        $this->users[] = user_save(new \stdClass(), $new_user);
    }

    /**
     * Cleans up the database from the created users.
     */
    public function down()
    {
        foreach ($this->users as $user) {
            user_delete($user->uid);
        }
    }
}
```

##### Auto Running Seeders
The DB seeder classes have an `auto_run` property, as shown in the example above, that
controls whether the seeder should run automatically before the testing stage begins.
However, you can also run the seeder manually by changing the `$auto_run` value to false
then using the static `seed()` method. For example, within a test class, you can run
`$seeder = UsersTableSeeder::seed()` which runs the `up()` method and returns an initialized seeder
object. Whenever done with the data, you can run `$seeder->down()` to rollback the changes. If you are
using the `DBTransaction` trait, you will not need to run the `down()` since transactions are
automatically rolled at the end of each test function.

Note that running the seeder manually in a test function with `DBTransaction` enabled,
means that the data is available only to that function and nothing else. However,
running it automatically, makes it available to the entire test suite. 

#### TripalTestCase
Test classes should extend the TripalTestCase class. Once extended, bootstrapping
Drupal and reading your `.env` file is done automatically when the first test is run.

```php
namespace Tests;

use StatonLab\TripalTestSuite\TripalTestCase;

class MyTest extends TripalTestCase {
}
```

### Using DB Transactions
Using DB transactions cleans up the database after every test by rolling back
the database to the original state before the test started. Therefore, anything
added to the database in one test function will not be available for the next
function. If you'd like data to be available for all of the tests, see [database
seeders](https://github.com/statonlab/TripalTestSuite#database-seeders) above.

To activate DB Transactions, simply add the DBTransaction trait to your test class:

```php
namespace Tests;

use StatonLab\TripalTestSuite\TripalTestCase;
use StatonLab\TripalTestSuite\DBTransaction;

class MyTest extends TripalTestCase {
	use DBTransaction;
}
```

The trait will automatically activate DB transactions and rollback the database when the test is finished.

**NOTE**: If the code you are testing requires a transaction, Postgres
will fail since it does not support nested transactions.

### Factories
DB factories provide a method to populate the database with fake data. Using factories, you
won't have to run SQL queries to populate the Database in every test. Since they are reusable,
you can define one factory for each table and use them across all tests.
Usage example:
```php
# Generates 100 controlled vocabularies.
# @return an array of vocabularies
$controlledVocabs = factory('chado.cv', 100)->create()
```

#### Defining Factories
Factories live in `tests/DataFactory.php`. If you don't have that file, create it. Note that this file
is auto created with `tripaltest init`.

Example DataFactory file:
```php
<?php

use StatonLab\TripalTestSuite\Database\Factory;

Factory::define('chado.cv', function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'definition' => $faker->text,
    ];
});
```

As shown in the example above, using `Factory::define()`, we can define new factories.
The define method takes the following parameters:

|Parameter|Type|Description|Example|
|---------|----|-----------|-------|
|$table|`string`|The table name preceded with the schema name if the schema is not public|`chado.cv` or `node`|
|$callback|`callable`|The function that generates the array. A `Faker\Generator` instance is automatically passed to the callable|see above for example|
|$primary_key|`string`|**OPTIONAL** The primary key for the given table. Primary keys auto discovered for CHADO tables only. If the factory wasn't able to find the primary key, an `Exception` will be thrown|`nid` or `cv_id`|
 
#### Using Factories
Once defined, factories can be used in test files directly or in database seeders.
Usage:
```php
# Create a single CV record
$cv = factory('chado.cv')->create();
echo "$cv->name\n";

# Create 100 CV records
$cvs = factory('chado.cv', 100)->create();
```

###### Overriding Defaults
Sometimes you need to override a column to be a static predictable value. The `create()` method accepts an array of values
to override the faker data with. Example:
```php
# Let's make sure the cvterm has a specific cv id
$cv = factory('chado.cv')->create();
$cv_term factory('chado.cvterm', 100)->create([
    'cv_id' => $cv->cv_id,
])
```
The above example creates 100 cv terms that have the same cv_id.

### Environment Variables
You can specify the Drupal web root path in `tests/.env`.
```bash
# tests/.env
DRUPAL_ROOT=/var/www/html
```

This allows TripalTestSuite to bootstrap the entire Drupal framework and make it available in your tests.

## License
TripalTestSuite is licensed under GPLv3.
