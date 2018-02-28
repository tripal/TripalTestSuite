TripalTestSuite is a composer package that handles
common test practices such as bootstrapping Drupal
before running the tests.

### Installation
Within your Drupal root path, run the following.
```bash
composer require statonlab/tripal-test-suite
```

In your `tests/bootstrap.php` file, include composer's autoload.php file. 
If the `tests/bootstrap.php` file does not exist, please create it.

```php
require '../vendor/autoload.php';
```

### Usage

#### TripalTestCase
Test classes should extend the TripalTestCase class. Once extended, bootstrapping 
Drupal and reading your `.env` file is done automatically when the first test is run.

```php
namespace Tests;

use Statonlab\TripalTestSuite;

class MyTest extends TripalTestCase {
}
```

### Using DB Transactions
Using DB transactions, cleans up the database after every test by rolling back
the database to the original state before the test started.

To activate DB Transactions, simply add the DBTransaction trait to your test class:

```php
namespace Tests;

use Statonlab\TripalTestSuite;

class MyTest extends TripalTestCase {
	use DBTransaction;
}
```

The trait will automatically activate DB transactions and rollback the database when the test is finished.

**NOTE**: If the code you are testing requires
a transaction, Postgres will fail since it does not support nested transactions.
