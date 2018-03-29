[![Build Status](https://travis-ci.org/statonlab/TripalTestSuite.svg?branch=master)](https://travis-ci.org/statonlab/TripalTestSuite) [![DOI](https://zenodo.org/badge/123318173.svg)](https://zenodo.org/badge/latestdoi/123318173)

TripalTestSuite is a composer package that handles
common test practices such as bootstrapping Drupal
before running the tests, creating test file, and creating
and managing database seeders (files that seed the database
with data for use in testing).

### Installation
Within your Drupal module path (e,g sites/all/my_module), run the following.
```bash
composer require statonlab/tripal-test-suite --dev
```

#### Automatic Set Up
This module will automatically configure your tests directory,
PHPUnit bootstrap files, and travis continuous integration file
as well as provide an example test to get you started. 

From your module's directory, execute:
```bash
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
Using DB transactions, cleans up the database after every test by rolling back
the database to the original state before the test started.

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

### Environment Variables
You can specify the Drupal web root path in `tests/.env`.
```bash
# tests/.env
DRUPAL_ROOT=/var/www/html
```

This allows TripalTestSuite to bootstrap the entire Drupal framework and make it available in your tests.

## License
TripalTestSuite is licensed under GPLv3.
